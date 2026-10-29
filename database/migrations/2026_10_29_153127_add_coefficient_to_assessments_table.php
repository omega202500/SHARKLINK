<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('assessments', function (Blueprint $table) {
        if (!Schema::hasColumn('assessments', 'coefficient')) {
            $table->integer('coefficient')->default(1)->after('course_offering_id');
        }
        if (!Schema::hasColumn('assessments', 'max_score')) {
            $table->decimal('max_score', 5, 2)->default(20.00)->after('coefficient');
        }
    });
}

public function down(): void
{
    Schema::table('assessments', function (Blueprint $table) {
        $table->dropColumn(['coefficient', 'max_score']);
    });
}
};