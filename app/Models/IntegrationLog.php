<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['service_name', 'request_data', 'response_data', 'status', 'error_message'];
}