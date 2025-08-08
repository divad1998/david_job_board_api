<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'user_job_applications';

    protected $fillable = [
        'first_name', 'last_name', 'email', 
        'phone', 'location', 'cv', 'user_id', 'user_job_id'
    ];

    protected $hidden = [
        'user_id', 'user_job_id', 'created_at', 'updated_at', 'id'
    ];

}
