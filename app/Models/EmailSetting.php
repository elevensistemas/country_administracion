<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $table = 'email_settings';

    protected $fillable = [
        'sender_name', 'sender_email', 'reply_to', 'provider', 
        'host', 'port', 'username', 'password', 'encryption', 
        'status', 'test_connection_status'
    ];
}