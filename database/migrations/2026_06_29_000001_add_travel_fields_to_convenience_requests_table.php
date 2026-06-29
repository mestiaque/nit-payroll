<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convenience_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('convenience_requests', 'from_location')) {
                $table->string('from_location')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('convenience_requests', 'to_location')) {
                $table->string('to_location')->nullable()->after('from_location');
            }
            if (!Schema::hasColumn('convenience_requests', 'travel_by')) {
                $table->string('travel_by')->nullable()->after('to_location');
            }
            if (!Schema::hasColumn('convenience_requests', 'attachment')) {
                $table->string('attachment')->nullable()->after('travel_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('convenience_requests', function (Blueprint $table) {
            foreach (['attachment', 'travel_by', 'to_location', 'from_location'] as $col) {
                if (Schema::hasColumn('convenience_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
