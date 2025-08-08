<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;

class JobController extends Controller
{
    function store(CreateJobRequest $request){
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $job = Job::create($data);

        $company = User::find($job->user_id, ['name', 'logo']);
        $job->company = $company->name;
        $job->company_logo = $company->logo;
        $job->makeHidden(['user_id']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Job Successfully Created.',
            'data'    => $job
        ], 201);
    }

    function index(Request $request){
        $userId = auth()->id();
        $query = Job::query();
        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $jobs = $query->where('user_id', $userId)->latest()->paginate(10);
        // Transform the jobs to include company name and logo
        $jobs->getCollection()->transform(function ($job) {
            $company = User::find($job->user_id, ['name', 'logo']);
            $job->company_name = $company ? $company->name : null;
            $job->company_logo = $company ? $company->logo : null;
            $job->makeHidden(['user_id']);
            return $job;
        });

        return response()->json([
            'status' => 'success',
            'message' => "Jobs fetched successfully.",
            'data'   => $jobs
        ]);
    }

    function update(UpdateJobRequest $request, $id){
        $userId = auth()->id();
        $job = Job::where(['user_id' => $userId, 'id'=> $id])->first();

        if (!$job) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Job not found.'
            ], 404);
        }

        $data = $request->validated();
        $job->update($data);

        $company = User::find($job->user_id, ['name', 'logo']);
        $job->company = $company->name;
        $job->company_logo = $company->logo;
        $job->makeHidden(['user_id']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Job updated successfully.',
            'data'    => $job
        ]);
    }

    function destroy($id) {
        $userId = auth()->id();
        $job = Job::where(['user_id' => $userId, 'id'=> $id])->first();

        if (!$job) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Job not found.'
            ], 404);
        }

        $job->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Job deleted successfully.',
        ]);
    }

    /**
     * Fetch a job's applications
     */
    function fetchApplications($job_id) {
        $userId = auth()->id();
        $job = Job::where(['id' => $job_id, 'user_id' => $userId])
                    ->select('id')
                    ->first();
        if(!$job) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid job."
            ], 404);
        } 

        $applications = JobApplication::where(['user_job_id' => $job->id, 'user_id' => $userId])
                                        ->paginate(10);
        //transform
        $applications->getCollection()->transform(function ($application) {
            $application->job_id = $application->user_job_id;
            return $application;
        });
        
        return response()->json([
            'status' => 'success',
            'message' => "Applications fetched successfully.",
            'data' => $applications
        ]);
    }
}
