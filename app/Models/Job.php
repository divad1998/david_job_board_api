<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'user_jobs';
     
    protected $fillable = [
        'title',
        'description',
        'location',
        'category',
        'benefits',
        'salary',
        'type',
        'work_condition',
        'user_id'
    ];

    function applications() {
        return $this->hasMany(JobApplication::class, 'user_job_id');
    }

    function user() {
        return $this->belongsTo(User::class);
    }
}
