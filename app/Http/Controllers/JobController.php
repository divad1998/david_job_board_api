<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobController extends Controller
{
    function store(CreateJobRequest $request){
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $job = Job::create($data);

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
        return response()->json([
            'status' => 'success',
            'message' => "Applications fetched successfully.",
            'data' => $applications
        ]);
    }
}
