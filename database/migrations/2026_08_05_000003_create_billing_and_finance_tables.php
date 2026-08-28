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
        Schema::create('billing_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period')->unique(); // e.g., "2026-08"
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft'); // draft, published, closed
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('functional_unit_id')->constrained()->onDelete('cascade');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('second_due_date')->nullable();
            $table->decimal('previous_balance', 15, 2)->default(0.00);
            $table->decimal('capital_amount', 15, 2)->default(0.00);
            $table->decimal('interest_amount', 15, 2)->default(0.00);
            $table->decimal('adjustments_amount', 15, 2)->default(0.00);
            $table->decimal('discount_amount', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->string('status')->default('draft'); // draft, published, sent, partial, paid, overdue, cancelled
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->string('concept');
            $table->decimal('amount', 15, 2);
            $table->string('category')->default('general'); // general, reserve_fund, extraordinary
            $table->timestamps();
        });

        Schema::create('account_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('functional_unit_id')->constrained()->onDelete('cascade');
            $table->string('type'); // debit, credit
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('description');
            $table->string('related_model_type')->nullable(); // Polymorphic link (e.g. Expense, Payment, Adjustment)
            $table->unsignedBigInteger('related_model_id')->nullable();
            $table->timestamps();

            $table->index(['related_model_type', 'related_model_id']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('lot_id')->constrained()->onDelete('cascade');
            $table->foreignId('functional_unit_id')->constrained()->onDelete('cascade');
            $table->date('payment_date');
            $table->date('import_date')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('bank')->nullable();
            $table->string('payment_method')->default('transfer'); // transfer, deposit, cash, portal_gateway
            $table->string('operation_number')->nullable();
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // administrative user approving this
            $table->string('source_channel')->default('portal'); // portal, admin, email, whatsapp, import
            $table->string('status')->default('pending'); // pending, review, approved, rejected, duplicate
            $table->timestamps();
        });

        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_movement_id')->constrained()->onDelete('cascade'); // The debt movement being settled
            $table->decimal('allocated_amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('account_movements');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('billing_periods');
    }
};
