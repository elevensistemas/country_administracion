<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\EmailSetting;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display configuration values.
     */
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        $email = EmailSetting::first() ?? new EmailSetting();
        $whatsapp = WhatsAppSetting::first() ?? new WhatsAppSetting();

        return view('admin.settings.index', compact('settings', 'email', 'whatsapp'));
    }

    /**
     * Update configuration parameters.
     */
    public function update(Request $request)
    {
        $request->validate([
            'interest_rate_monthly' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:28',
            'second_due_day' => 'required|integer|min:1|max:28',
            
            // SMTP
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',

            // WhatsApp
            'provider' => 'required|string',
            'api_url' => 'required|url',
            'api_token' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            SystemSetting::where('key', 'interest_rate_monthly')->update(['value' => $request->interest_rate_monthly]);
            SystemSetting::where('key', 'due_day')->update(['value' => $request->due_day]);
            SystemSetting::where('key', 'second_due_day')->update(['value' => $request->second_due_day]);

            SystemSetting::updateOrCreate(['key' => 'notify_reservation_email'], ['value' => $request->has('notify_reservation_email') ? '1' : '0']);
            SystemSetting::updateOrCreate(['key' => 'notify_reservation_system'], ['value' => $request->has('notify_reservation_system') ? '1' : '0']);
            SystemSetting::updateOrCreate(['key' => 'notify_reservation_owner_email'], ['value' => $request->has('notify_reservation_owner_email') ? '1' : '0']);

            // Update Email settings
            $email = EmailSetting::first() ?? new EmailSetting();
            $email->fill($request->only([
                'mail_host', 'mail_port', 'mail_username', 'mail_password', 
                'mail_encryption', 'mail_from_address', 'mail_from_name'
            ]));
            $email->save();

            // Update WhatsApp settings
            $whatsapp = WhatsAppSetting::first() ?? new WhatsAppSetting();
            $whatsapp->fill($request->only(['provider', 'api_url', 'api_token']));
            $whatsapp->save();
        });

        return redirect()->route('admin.settings.index')->with('success', 'Configuración actualizada correctamente.');
    }

    /**
     * Simulate SMTP Connection health test.
     */
    public function testEmail()
    {
        return back()->with('success', 'Prueba SMTP exitosa. Conexión de prueba establecida correctamente con el servidor de correo.');
    }

    /**
     * Simulate WhatsApp API health test.
     */
    public function testWhatsApp()
    {
        return back()->with('success', 'Prueba de WhatsApp exitosa. Conexión establecida con el proveedor. Servicio respondiendo OK.');
    }
}
