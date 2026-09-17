<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(dirname($this->app->basePath()) . '/index.php')) {
            $this->app->usePublicPath(dirname($this->app->basePath()));
        }
    }

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Rate Limiter for security device sync
        \Illuminate\Support\Facades\RateLimiter::for('security-device', function (\Illuminate\Http\Request $request) {
            $token = $request->bearerToken() ?: $request->ip();
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($token);
        });

        // Load dynamic mail config from database
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('email_settings')) {
                $emailSetting = \App\Models\EmailSetting::first();
                if ($emailSetting) {
                    config([
                        'mail.mailers.smtp.host' => $emailSetting->mail_host,
                        'mail.mailers.smtp.port' => $emailSetting->mail_port,
                        'mail.mailers.smtp.username' => $emailSetting->mail_username,
                        'mail.mailers.smtp.password' => $emailSetting->mail_password,
                        'mail.mailers.smtp.encryption' => $emailSetting->mail_encryption ?: null,
                        'mail.from.address' => $emailSetting->mail_from_address,
                        'mail.from.name' => $emailSetting->mail_from_name,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Safe fallback
        }

        view()->composer(['layouts.app', 'layouts.owner'], function ($view) {
            if (auth()->check()) {
                $unreadNotificationsCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();
                $unreadNotifications = \App\Models\Notification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                $view->with(compact('unreadNotificationsCount', 'unreadNotifications'));
            }
        });
    }
}
