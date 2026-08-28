<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');

// Force Password Change & Terms Acceptance
Route::middleware(['auth'])->group(function () {
    Route::get('/password/force-change', [PasswordController::class, 'showForceChangeForm'])->name('password.force_change');
    Route::post('/password/force-change', [PasswordController::class, 'forceChange'])->name('password.force_change.post');
});

// User Preferences / Theme toggle (accessible by anyone logged in or not)
Route::post('/preferences/theme', [UserPreferenceController::class, 'updateTheme'])->name('preferences.theme');

// ==========================================
// AREA ADMINISTRATIVA
// ==========================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,superadmin,operator,accounting'])
    ->group(function () {

        // Dashboard
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Owners (Propietarios)
        Route::resource('owners', \App\Http\Controllers\Admin\OwnerController::class);

        // Users (Usuarios)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/resend-invite', [\App\Http\Controllers\Admin\UserController::class, 'resendInvite'])->name('users.resend-invite');

        // Lots & Functional Units (Lotes y Unidades Funcionales)
        Route::resource('lots', \App\Http\Controllers\Admin\LotController::class);
        Route::resource('functional-units', \App\Http\Controllers\Admin\FunctionalUnitController::class);

        // Lot History (Historia del Lote)
        Route::get('lots/{lot}/history', [\App\Http\Controllers\Admin\LotHistoryController::class, 'show'])->name('lots.history');
        Route::post('lots/{lot}/history/note', [\App\Http\Controllers\Admin\LotHistoryController::class, 'storeNote'])->name('lots.history.note');
        Route::get('lots/{lot}/history/export', [\App\Http\Controllers\Admin\LotHistoryController::class, 'export'])->name('lots.history.export');

        // General History (Historial General)
        Route::get('history', [\App\Http\Controllers\Admin\LotHistoryController::class, 'index'])->name('history.index');

        // Follow Ups (Seguimientos)
        Route::get('follow-ups', [\App\Http\Controllers\Admin\FollowUpController::class, 'index'])->name('follow-ups.index');
        Route::post('follow-ups/{followUp}/status', [\App\Http\Controllers\Admin\FollowUpController::class, 'updateStatus'])->name('follow-ups.status');
        Route::post('lots/{lot}/follow-up', [\App\Http\Controllers\Admin\FollowUpController::class, 'store'])->name('follow-ups.store');

        // Billing & Expenses (Expensas)
        Route::get('expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/create-period', [\App\Http\Controllers\Admin\ExpenseController::class, 'createPeriod'])->name('expenses.create-period');
        Route::post('expenses/create-period', [\App\Http\Controllers\Admin\ExpenseController::class, 'storePeriod'])->name('expenses.store-period');
        Route::post('expenses/generate', [\App\Http\Controllers\Admin\ExpenseController::class, 'generate'])->name('expenses.generate');
        Route::post('expenses/{expense}/publish', [\App\Http\Controllers\Admin\ExpenseController::class, 'publish'])->name('expenses.publish');
        Route::get('expenses/{expense}/pdf', [\App\Http\Controllers\Admin\ExpenseController::class, 'downloadPdf'])->name('expenses.pdf');

        // Current Accounts (Cuenta Corriente)
        Route::get('accounting', [\App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('accounting.index');
        Route::get('accounting/functional-unit/{functionalUnit}', [\App\Http\Controllers\Admin\AccountingController::class, 'showUnit'])->name('accounting.show');
        Route::post('accounting/functional-unit/{functionalUnit}/adjustment', [\App\Http\Controllers\Admin\AccountingController::class, 'storeAdjustment'])->name('accounting.adjustment');

        // Payments Reconciliation (Pagos y Comprobantes)
        Route::get('payments/auto-reconcile/simulate', [\App\Http\Controllers\Admin\PaymentController::class, 'autoReconcileSimulate'])->name('payments.auto-reconcile.simulate');
        Route::post('payments/auto-reconcile/apply', [\App\Http\Controllers\Admin\PaymentController::class, 'autoReconcileApply'])->name('payments.auto-reconcile.apply');
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{payment}/reconcile', [\App\Http\Controllers\Admin\PaymentController::class, 'reconcile'])->name('payments.reconcile');
        Route::post('payments/{payment}/revert', [\App\Http\Controllers\Admin\PaymentController::class, 'revert'])->name('payments.revert');
        Route::post('payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('payments/{payment}/mark-review', [\App\Http\Controllers\Admin\PaymentController::class, 'markReview'])->name('payments.mark-review');

        // Claims / Tickets (Reclamos)
        Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
        Route::post('tickets/{ticket}/message', [\App\Http\Controllers\Admin\TicketController::class, 'storeMessage'])->name('tickets.message');
        Route::post('tickets/{ticket}/internal-note', [\App\Http\Controllers\Admin\TicketController::class, 'storeInternalNote'])->name('tickets.internal-note');

        // News (Novedades)
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        // Communications (Comunicaciones)
        Route::get('comms', [\App\Http\Controllers\Admin\CommunicationController::class, 'index'])->name('comms.index');
        Route::get('comms/create', [\App\Http\Controllers\Admin\CommunicationController::class, 'create'])->name('comms.create');
        Route::post('comms', [\App\Http\Controllers\Admin\CommunicationController::class, 'store'])->name('comms.store');
        Route::get('comms/{communication}', [\App\Http\Controllers\Admin\CommunicationController::class, 'show'])->name('comms.show');

        // Documents (Repositorio de Documentos)
        Route::get('documents', [\App\Http\Controllers\Admin\DocumentController::class, 'index'])->name('documents.index');
        Route::post('documents', [\App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('documents.store');
        Route::post('documents/{document}/version', [\App\Http\Controllers\Admin\DocumentController::class, 'storeVersion'])->name('documents.version');
        Route::get('documents/versions/{version}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'downloadVersion'])->name('documents.download-version');
        Route::post('documents/{document}/archive', [\App\Http\Controllers\Admin\DocumentController::class, 'archive'])->name('documents.archive');

        // Adoption Module (Adopción de Usuarios)
        Route::get('adoption', [\App\Http\Controllers\Admin\AdoptionController::class, 'index'])->name('adoption.index');
        Route::post('adoption/campaign', [\App\Http\Controllers\Admin\AdoptionController::class, 'sendCampaign'])->name('adoption.campaign');

        // Reports (Reportes)
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');

        // Audit Logs (Auditoría)
        Route::get('audit', [\App\Http\Controllers\Admin\AuditController::class, 'index'])->name('audit.index');

        // Settings (Configuraciones)
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/email/test', [\App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->name('settings.email-test');
        Route::post('settings/whatsapp/test', [\App\Http\Controllers\Admin\SettingController::class, 'testWhatsApp'])->name('settings.whatsapp-test');

        // Import module
        Route::get('imports', [\App\Http\Controllers\Admin\ImportController::class, 'index'])->name('imports.index');
        Route::post('imports/upload', [\App\Http\Controllers\Admin\ImportController::class, 'upload'])->name('imports.upload');
        Route::get('imports/{import}/validate', [\App\Http\Controllers\Admin\ImportController::class, 'showValidation'])->name('imports.validate');
        Route::post('imports/{import}/process', [\App\Http\Controllers\Admin\ImportController::class, 'process'])->name('imports.process');

        // Zonas Comunes y Reservas (Administración)
        Route::resource('common-areas', \App\Http\Controllers\Admin\CommonAreaController::class);
        Route::get('reservations', [\App\Http\Controllers\Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::post('reservations/{reservation}/status', [\App\Http\Controllers\Admin\ReservationController::class, 'updateStatus'])->name('reservations.status');

        // Proveedores y Facturas de Compra (weekly cashflow egresos)
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);
        Route::get('supplier-invoices/print', [\App\Http\Controllers\Admin\SupplierInvoiceController::class, 'print'])->name('supplier-invoices.print');
        Route::resource('supplier-invoices', \App\Http\Controllers\Admin\SupplierInvoiceController::class);

        // Notificaciones del sistema
        Route::post('notifications/{notification}/read', function (\App\Models\Notification $notification) {
            $notification->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        })->name('notifications.read');

        Route::post('notifications/read-all', function () {
            \App\Models\Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
            return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
        })->name('notifications.read-all');
});

// ==========================================
// PORTAL DEL PROPIETARIO / INQUILINO
// ==========================================
Route::prefix('owner')
    ->name('owner.')
    ->middleware(['auth', 'role:owner,tenant,board'])
    ->group(function () {

        // Dashboard Propietario
        Route::get('/', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');

        // Mis Expensas
        Route::get('expenses', [\App\Http\Controllers\Owner\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/{expense}/download', [\App\Http\Controllers\Owner\ExpenseController::class, 'downloadPdf'])->name('expenses.download');

        // Mi Cuenta Corriente
        Route::get('accounting', [\App\Http\Controllers\Owner\AccountingController::class, 'index'])->name('accounting.index');

        // Informar Pago
        Route::get('payments/report', [\App\Http\Controllers\Owner\PaymentController::class, 'create'])->name('payments.report');
        Route::post('payments/report', [\App\Http\Controllers\Owner\PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/history', [\App\Http\Controllers\Owner\PaymentController::class, 'index'])->name('payments.history');

        // Mis Reclamos
        Route::resource('tickets', \App\Http\Controllers\Owner\TicketController::class)->except(['destroy', 'edit', 'update']);
        Route::post('tickets/{ticket}/message', [\App\Http\Controllers\Owner\TicketController::class, 'storeMessage'])->name('tickets.message');

        // Novedades
        Route::get('news', [\App\Http\Controllers\Owner\NewsController::class, 'index'])->name('news.index');
        Route::get('news/{news}', [\App\Http\Controllers\Owner\NewsController::class, 'show'])->name('news.show');

        // Documentos
        Route::get('documents', [\App\Http\Controllers\Owner\DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/versions/{version}/download', [\App\Http\Controllers\Owner\DocumentController::class, 'downloadVersion'])->name('documents.download-version');

        // Historia Pública del Lote
        Route::get('history', [\App\Http\Controllers\Owner\DashboardController::class, 'lotHistory'])->name('history');

        // Mi Perfil
        Route::get('profile', [\App\Http\Controllers\Owner\ProfileController::class, 'show'])->name('profile.show');
        Route::post('profile', [\App\Http\Controllers\Owner\ProfileController::class, 'update'])->name('profile.update');

        // Zonas Comunes y Reservas (Vecinos)
        Route::get('reservations', [\App\Http\Controllers\Owner\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/create/{common_area}', [\App\Http\Controllers\Owner\ReservationController::class, 'create'])->name('reservations.create');
        Route::post('reservations', [\App\Http\Controllers\Owner\ReservationController::class, 'store'])->name('reservations.store');
        Route::post('reservations/{reservation}/cancel', [\App\Http\Controllers\Owner\ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Autorizaciones de Invitados
        Route::get('guests', [\App\Http\Controllers\Owner\GuestController::class, 'index'])->name('guests.index');
        Route::get('guests/create', [\App\Http\Controllers\Owner\GuestController::class, 'create'])->name('guests.create');
        Route::post('guests', [\App\Http\Controllers\Owner\GuestController::class, 'store'])->name('guests.store');
        Route::delete('guests/{guest}', [\App\Http\Controllers\Owner\GuestController::class, 'destroy'])->name('guests.destroy');
        Route::get('guests/{guest}/qr', [\App\Http\Controllers\Owner\GuestController::class, 'showQr'])->name('guests.qr');

        // Mi Propiedad (Residentes y Vehículos)
        Route::get('property', [\App\Http\Controllers\Owner\PropertyController::class, 'index'])->name('property.index');
        Route::post('property/request-change', [\App\Http\Controllers\Owner\PropertyController::class, 'requestChange'])->name('property.request-change');
});
