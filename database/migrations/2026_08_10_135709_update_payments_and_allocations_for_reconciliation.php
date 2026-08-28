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
        Schema::table('payments', function (Blueprint $table) {
            // Make relationship columns nullable to allow unmatched payments
            $table->unsignedBigInteger('owner_id')->nullable()->change();
            $table->unsignedBigInteger('lot_id')->nullable()->change();
            $table->unsignedBigInteger('functional_unit_id')->nullable()->change();

            // Add fields for auto-matching and auditing
            $table->integer('matching_score')->nullable();
            $table->unsignedBigInteger('matched_debit_id')->nullable();
            $table->string('reconciliation_method')->nullable(); // automatic, manual
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamp('reverted_at')->nullable();
            $table->foreignId('reverted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reversion_reason')->nullable();
        });

        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('method')->nullable(); // automatic, manual
            $table->decimal('previous_balance', 15, 2)->nullable();
            $table->decimal('posterior_balance', 15, 2)->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'method', 'previous_balance', 'posterior_balance', 'notes']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['reverted_by']);
            $table->dropColumn([
                'matching_score',
                'matched_debit_id',
                'reconciliation_method',
                'reconciled_at',
                'reverted_at',
                'reverted_by',
                'reversion_reason'
            ]);

            // Revert columns to not nullable (note: since DB was empty, we can just do this)
            $table->unsignedBigInteger('owner_id')->nullable(false)->change();
            $table->unsignedBigInteger('lot_id')->nullable(false)->change();
            $table->unsignedBigInteger('functional_unit_id')->nullable(false)->change();
        });
    }
};
