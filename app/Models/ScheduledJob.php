<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledJob extends Model
{
    protected $fillable = ['name', 'cron', 'last_run_at', 'next_run_at', 'status', 'output'];

    protected $casts = [
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];
}