<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobApplicationRequest;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    /**
     * handles a guest's application for a job.
     */
    function apply(JobApplicationRequest $request, $job_id) {
        $validatedData = $request->validated();

        $job = Job::find($job_id, ['id', 'user_id']);
        if(!$job) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid job.'], 404);
        }
        $applicationExists = JobApplication::where([
            'user_job_id' => $job->id, 
            'email' => $validatedData['email']
            ])->exists();
        
        if($applicationExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incomplete. You applied already.'], 422);
        }

        if ($request->hasFile('cv')) {
            $filePath = $request->file('cv')->store('cvs', 'public');
            $validatedData['cv'] = asset('storage/' . $filePath);
        }
        $validatedData['user_job_id'] = $job->id;
        $validatedData['user_id'] = $job->user_id;

        JobApplication::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Application successfully submitted.'], 201);
    }

    /**
     * Handles a guess viewing all jobs.
     */
    function viewJobs(Request $request) {
        $query = Job::query();
        if ($jobTitle = $request->query('job_title')) {
            $query->where('title', 'like', "%{$jobTitle}%");
        }
        if ($location = $request->query('location')) {
            $query->where('location', 'like', "%{$location}%");
        }
        $jobs = $query->distinct()->paginate(10);
        $jobs->getCollection()->transform(function ($job) {
            $company = User::find($job->user_id, ['name', 'logo']);
            $job->company_name = $company ? $company->name : null;
            $job->company_logo = $company ? $company->logo : null;
            $job->makeHidden(['user_id']);
            return $job;
        });

        return response()->json([
            'status' => 'success', 
            'message' => 'Jobs fetched successfully.',
            'data' => $jobs
        ]);
    }

    function viewJobById($job_id) {
        $job = Job::with('user:id,name,logo')->find($job_id);
        if(!$job) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid job."
            ], 404);
        } 

        $job->company = $job->user->name;
        $job->company_logo = $job->user->logo;
        unset($job->user);

        return response()->json([
            'status' => 'success',
            'message' => "Job fetched successfully.",
            'data' => $job
        ]);
    }
}
