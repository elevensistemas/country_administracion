<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuestAuthorization;
use App\Models\SecurityDevice;
use App\Models\SecuritySyncLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SecuritySyncController extends Controller
{
    /**
     * Authenticate security device from Bearer token.
     */
    private function authenticateDevice(Request $request): ?SecurityDevice
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $plainToken = trim(substr($authHeader, 7));
        if (empty($plainToken)) {
            return null;
        }

        $tokenHash = hash('sha256', $plainToken);

        return SecurityDevice::where('api_token_hash', $tokenHash)
            ->where('is_active', true)
            ->whereNull('revoked_at')
            ->first();
    }

    /**
     * Sync authorizations for the security guardhouse.
     * GET /api/security/sync?since=<cursor>&limit=100
     */
    public function sync(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'No autorizado. Token de dispositivo inválido o revocado.'
            ], 401);
        }

        $limit = min((int) $request->input('limit', 100), 500);
        $since = $request->input('since');

        $query = GuestAuthorization::with(['lot', 'user']);

        if (!empty($since)) {
            try {
                $sinceDate = Carbon::parse($since);
                $query->where('updated_at', '>', $sinceDate);
            } catch (\Exception $e) {
                // If invalid date format, ignore or treat as beginning
            }
        } elseif (!$request->boolean('full_backfill', false)) {
            // Default temporal security window: only active or updated in last 60 days
            $query->where(function ($q) {
                $q->where('updated_at', '>=', now()->subDays(60))
                  ->orWhere('status', 'active');
            });
        }

        $records = $query->orderBy('updated_at', 'asc')
            ->orderBy('id', 'asc')
            ->take($limit + 1)
            ->get();

        $hasMore = $records->count() > $limit;
        if ($hasMore) {
            $records = $records->take($limit);
        }

        $lastRecord = $records->last();
        $nextCursor = $lastRecord ? $lastRecord->updated_at->toISOString() : ($since ?: now()->toISOString());

        $formattedData = $records->map(function (GuestAuthorization $auth) {
            return [
                'uuid' => $auth->uuid,
                'lot_number' => $auth->lot?->number ?? 'N/A',
                'lot_id' => $auth->lot_id,
                'owner_name' => $auth->user?->full_name ?? 'Propietario',
                'owner_email' => $auth->user?->email,
                'type' => $auth->type,
                'guest_name' => $auth->name,
                'guest_last_name' => $auth->last_name,
                'guest_full_name' => $auth->full_name,
                'guest_dni' => $auth->dni,
                'license_plate' => $auth->license_plate,
                'status' => $auth->status,
                'valid_from' => $auth->valid_from?->toDateTimeString() ?? ($auth->visit_date ? "{$auth->visit_date->toDateString()} 00:00:00" : null),
                'valid_until' => $auth->valid_until?->toDateTimeString() ?? ($auth->visit_date ? "{$auth->visit_date->toDateString()} 23:59:59" : null),
                'cancelled_at' => $auth->cancelled_at?->toISOString(),
                'notes' => $auth->notes,
                'qr_code' => $auth->qr_code ?: $auth->uuid,
                'updated_at' => $auth->updated_at->toISOString(),
            ];
        });

        // Update device last seen
        $device->update([
            'last_seen_at' => now(),
            'last_sync_cursor' => $nextCursor,
        ]);

        // Log sync operation
        SecuritySyncLog::create([
            'security_device_id' => $device->id,
            'since_cursor' => $since,
            'records_count' => $records->count(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
        ]);

        return response()->json([
            'success' => true,
            'server_time' => now()->toISOString(),
            'device' => [
                'name' => $device->name,
                'identifier' => $device->device_identifier,
            ],
            'count' => $formattedData->count(),
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
            'data' => $formattedData,
        ]);
    }

    /**
     * Record a check-in event from the guardhouse.
     * POST /api/security/check-in
     */
    public function checkIn(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['success' => false, 'error' => 'No autorizado.'], 401);
        }

        $request->validate([
            'uuid' => 'required|string',
            'checked_in_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $auth = GuestAuthorization::where('uuid', $request->uuid)->first();
        if (!$auth) {
            return response()->json(['success' => false, 'error' => 'Autorización no encontrada.'], 404);
        }

        if ($auth->status === 'cancelled') {
            return response()->json(['success' => false, 'error' => 'Esta autorización ha sido cancelada por el propietario.'], 422);
        }

        if ($auth->valid_until && now()->gt($auth->valid_until)) {
            return response()->json(['success' => false, 'error' => 'Esta autorización ha expirado (vigencia finalizada).'], 422);
        }

        $auth->update([
            'status' => 'used',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ingreso registrado correctamente.',
            'authorization' => [
                'uuid' => $auth->uuid,
                'guest' => $auth->full_name,
                'lot' => $auth->lot?->number,
                'status' => $auth->status,
            ]
        ]);
    }
}