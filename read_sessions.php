<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$sessionDir = storage_path('framework/sessions');
$files = glob($sessionDir . '/*');

foreach ($files as $file) {
    if (basename($file) === '.gitignore') continue;
    
    $content = file_get_contents($file);
    // Laravel sessions are serialized PHP arrays (unserialized using unserialize())
    try {
        $data = unserialize($content);
        if ($data) {
            $mtime = filemtime($file);
            $userId = isset($data['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d']) ? $data['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'] : (isset($data['auth']) ? $data['auth'] : 'guest');
            if ($userId === 'guest') {
                // Check if any key starts with login_web
                foreach ($data as $k => $v) {
                    if (strpos($k, 'login_web_') === 0) {
                        $userId = $v;
                        break;
                    }
                }
            }
            
            $activeLotId = isset($data['active_lot_id']) ? $data['active_lot_id'] : 'not_set';
            
            echo "Session File: " . basename($file) . "\n";
            echo " - Last Modified: " . date('Y-m-d H:i:s', $mtime) . "\n";
            echo " - User ID: " . var_export($userId, true) . "\n";
            if ($userId !== 'guest') {
                $user = \App\Models\User::find($userId);
                echo " - User Name: " . ($user ? $user->full_name : 'unknown') . "\n";
            }
            echo " - Active Lot ID: " . var_export($activeLotId, true) . "\n";
            echo "\n";
        }
    } catch (\Exception $e) {
        // Ignored
    }
}
