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
        Schema::table('performances', function (Blueprint $table) {
            $table->date('report_start_date')->nullable()->after('goals');
            $table->date('report_end_date')->nullable()->after('report_start_date');
            $table->unsignedInteger('present_days')->default(0)->after('report_end_date');
            $table->unsignedInteger('late_days')->default(0)->after('present_days');
            $table->unsignedInteger('absent_days')->default(0)->after('late_days');
            $table->unsignedInteger('leave_days')->default(0)->after('absent_days');
            $table->unsignedInteger('approved_leave_requests')->default(0)->after('leave_days');
            $table->decimal('dress_score', 3, 2)->default(0)->after('approved_leave_requests');
            $table->decimal('behavior_score', 3, 2)->default(0)->after('dress_score');
            $table->text('dress_note')->nullable()->after('behavior_score');
            $table->text('behavior_note')->nullable()->after('dress_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->dropColumn([
                'report_start_date',
                'report_end_date',
                'present_days',
                'late_days',
                'absent_days',
                'leave_days',
                'approved_leave_requests',
                'dress_score',
                'behavior_score',
                'dress_note',
                'behavior_note',
            ]);
        });
    }
};
