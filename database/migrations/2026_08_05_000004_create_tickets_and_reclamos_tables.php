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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // creator user
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('new'); // new, assigned, in_progress, waiting_response, resolved, closed, reopened
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // operator assigned
            $table->integer('rating')->nullable(); // 1 to 5 stars
            $table->text('rating_comments')->nullable();
            $table->string('source_channel')->default('portal'); // portal, admin, email, whatsapp
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // sender user
            $table->text('message');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_message_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });

        Schema::create('ticket_internal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // admin user
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_internal_notes');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_categories');
    }
};
