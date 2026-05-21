<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_sheets', function (Blueprint $table) {
            if (!Schema::hasColumn('salary_sheets', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('salary_sheets', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('processed_by');
            }
            if (!Schema::hasColumn('salary_sheets', 'company_pf')) {
                $table->decimal('company_pf', 12, 2)->default(0)->after('provident_fund');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_sheets', function (Blueprint $table) {
            $cols = ['processed_by', 'updated_by', 'company_pf'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('salary_sheets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
