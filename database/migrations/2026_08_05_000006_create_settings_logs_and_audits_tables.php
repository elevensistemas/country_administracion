<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('provider')->default('smtp'); // smtp, mailgun, sendgrid, ses, gmail, outlook
            $table->string('host')->nullable();
            $table->integer('port')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // Crypted in service
            $table->string('encryption')->nullable(); // tls, ssl, null
            $table->boolean('status')->default(false);
            $table->string('test_connection_status')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('meta'); // meta, twilio, dialog360
            $table->boolean('status')->default(false);
            $table->string('phone_number')->nullable();
            $table->string('phone_number_id')->nullable();
            $table->string('business_account_id')->nullable();
            $table->text('token')->nullable(); // Crypted in service
            $table->string('secret')->nullable(); // Crypted
            $table->string('webhook_url')->nullable();
            $table->string('display_name')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->string('status')->default('success'); // success, fail
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('success'); // success, failed, blocked
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // administrative user performing change
            $table->string('action'); // create, update, delete, restore
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->longText('old_values')->nullable(); // JSON
            $table->longText('new_values')->nullable(); // JSON
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['model_type', 'model_id']);
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->string('type'); // users, owners, lots, functional_units
            $table->integer('total_rows')->default(0);
            $table->integer('valid_rows')->default(0);
            $table->integer('invalid_rows')->default(0);
            $table->string('status')->default('pending'); // pending, validating, validated, processing, completed, failed
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained()->onDelete('cascade');
            $table->integer('row_number');
            $table->longText('data'); // JSON original data
            $table->longText('errors')->nullable(); // JSON list of errors
            $table->string('status')->default('pending'); // pending, valid, invalid, imported, failed
            $table->timestamps();
        });

        Schema::create('scheduled_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cron')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->string('status')->default('pending'); // pending, running, success, failed
            $table->text('output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_jobs');
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('imports');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('login_logs');
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('whatsapp_settings');
        Schema::dropIfExists('email_settings');
    }
};
