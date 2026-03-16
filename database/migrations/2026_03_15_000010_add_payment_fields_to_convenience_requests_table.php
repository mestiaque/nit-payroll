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
        Schema::table('convenience_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('convenience_requests', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('convenience_requests', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('convenience_requests', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('convenience_requests', 'paid_by')) {
                $table->unsignedBigInteger('paid_by')->nullable()->after('payment_note');
                $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('convenience_requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convenience_requests', function (Blueprint $table) {
            if (Schema::hasColumn('convenience_requests', 'paid_by')) {
                $table->dropForeign(['paid_by']);
            }
            if (Schema::hasColumn('convenience_requests', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('convenience_requests', 'paid_by')) {
                $table->dropColumn('paid_by');
            }
            if (Schema::hasColumn('convenience_requests', 'payment_note')) {
                $table->dropColumn('payment_note');
            }
            if (Schema::hasColumn('convenience_requests', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('convenience_requests', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
