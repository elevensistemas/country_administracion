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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('summary')->nullable();
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->string('visibility')->default('public'); // public, internal
            $table->string('recipients_type')->default('all'); // all, debt, no_debt, active, inactive, custom
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('channels')->default('portal'); // JSON or comma-separated list: portal, email, whatsapp
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('channels')->default('email'); // email, whatsapp, both
            $table->timestamps();
        });

        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('attachment_path')->nullable();
            $table->string('channels')->default('portal'); // portal, email, whatsapp, all
            $table->string('target_type')->default('all'); // all, lot_owners, lot_tenants, debt_only, custom
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('lot_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('preferred_channel')->default('email');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamps();
        });

        Schema::create('communication_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('communication_recipient_id')->constrained('communication_recipients')->onDelete('cascade');
            $table->string('channel'); // email, whatsapp, portal
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed
            $table->string('provider_message_id')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });

        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('visibility')->default('public'); // public, owners_only, board_only, internal
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('version')->default('1.0');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('communication_deliveries');
        Schema::dropIfExists('communication_recipients');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('communication_templates');
        Schema::dropIfExists('news');
    }
};
