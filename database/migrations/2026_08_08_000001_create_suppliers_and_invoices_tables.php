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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('cuit')->unique();
            $table->string('business_name');
            $table->string('category')->default('Servicios');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('cbu_alias')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('invoice_number');
            $table->string('concept');
            $table->decimal('amount', 15, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('file_path')->nullable();
            $table->string('status')->default('pending'); // pending, scheduled, paid, voided
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('suppliers');
    }
};
