<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PublicLandingController extends Controller
{
    /**
     * Display the public institutional landing page for Club de Campo La Ranita.
     */
    public function index()
    {
        return view('public.landing');
    }

    /**
     * Handle contact form submission from the landing page.
     */
    public function contact(Request $request)
    {
        // 1. Antispam Honeypot validation (must be empty)
        if (!empty($request->input('website_hp'))) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Consulta recibida con éxito.']);
            }
            return back()->with('contact_success', 'Gracias por contactarte. Responderemos a la brevedad.');
        }

        // 2. Validate input fields
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'topic' => 'required|string|in:general,lotes,administracion,visita,otro',
            'message' => 'required|string|min:10|max:2000',
            'privacy' => 'accepted',
        ], [
            'name.required' => 'Por favor, ingresá tu nombre completo.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'email.required' => 'Por favor, ingresá un correo electrónico válido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'topic.required' => 'Por favor, seleccioná el motivo de tu consulta.',
            'message.required' => 'Por favor, escribí tu mensaje o consulta.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'privacy.accepted' => 'Debes aceptar la política de privacidad para enviar la consulta.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput()->with('contact_error', 'Por favor, revisá los campos del formulario.');
        }

        $data = $validator->validated();

        // 3. Log contact message for administration
        Log::info('Public Contact Form Submission - La Ranita Country Club', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? 'N/A',
            'topic' => $data['topic'],
            'message_length' => strlen($data['message']),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        $successMsg = '¡Gracias por contactarte con el Club de Campo La Ranita! Hemos recibido tu mensaje y nos comunicaremos con vos a la brevedad.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg
            ]);
        }

        return redirect()->to(route('public.landing') . '#contacto')->with('contact_success', $successMsg);
    }
}
