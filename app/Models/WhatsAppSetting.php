<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'provider', 'status', 'phone_number', 'phone_number_id', 
        'business_account_id', 'token', 'secret', 'webhook_url', 'display_name'
    ];
}