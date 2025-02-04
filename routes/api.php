<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    //business profile
    Route::get('user', [UserController::class, 'getUser'])->middleware('auth:sanctum');

    Route::prefix('my/jobs')->middleware('auth:sanctum')->controller(JobController::class)->group(function () {
        Route::post('', 'store');         
        Route::put('{id}', 'update');      
        Route::delete('{id}', 'destroy');  
        Route::get('', 'index');  
        Route::get('/{job_id}/applications', 'fetchApplications');
    });
    
    Route::prefix('jobs')->controller(JobApplicationController::class)->group(function() {
        Route::post('{job_id}/apply', 'apply');
        Route::get('', 'viewJobs');
        Route::get('/{job_id}', 'viewJobById');
    }); 
});






