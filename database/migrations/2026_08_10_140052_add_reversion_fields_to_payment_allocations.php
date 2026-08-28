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
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->string('status')->default('active'); // active, reverted
            $table->timestamp('reverted_at')->nullable();
            $table->foreignId('reverted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reversion_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropForeign(['reverted_by']);
            $table->dropColumn(['status', 'reverted_at', 'reverted_by', 'reversion_reason']);
        });
    }
};
