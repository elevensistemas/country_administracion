<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

Auth::loginUsingId(66);
$user = Auth::user();
$lots = $user->functionalUnits->map(fn($u) => $u->lot)->unique('id');
echo "User: " . $user->full_name . " (ID: " . $user->id . ")\n";
echo "Lots count: " . $lots->count() . "\n";
foreach ($lots as $l) {
    echo " - Lot ID: " . $l->id . " Number: " . $l->number . "\n";
}
$activeLotId = session('active_lot_id');
echo "Session active_lot_id: " . var_export($activeLotId, true) . "\n";
$activeLot = $lots->firstWhere('id', $activeLotId);
if (!$activeLot) {
    $activeLot = $lots->first();
    echo "Fallback activeLot: Lot ID " . ($activeLot ? $activeLot->id : 'null') . "\n";
} else {
    echo "Resolved activeLot: Lot ID " . $activeLot->id . "\n";
}
if ($activeLot) {
    $guests = \App\Models\GuestAuthorization::where('lot_id', $activeLot->id)->get();
    echo "Guests count for Lot ID " . $activeLot->id . ": " . $guests->count() . "\n";
}
