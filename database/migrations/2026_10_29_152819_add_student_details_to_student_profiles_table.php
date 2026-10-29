<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('student_profiles', function (Blueprint $table) {
        if (!Schema::hasColumn('student_profiles', 'birth_date')) {
            $table->date('birth_date')->nullable()->after('user_id');
        }
        if (!Schema::hasColumn('student_profiles', 'birth_place')) {
            $table->string('birth_place')->nullable()->after('birth_date');
        }
        if (!Schema::hasColumn('student_profiles', 'gender')) {
            $table->enum('gender', ['M', 'F'])->default('M')->after('birth_place');
        }
    });
}

public function down(): void
{
    Schema::table('student_profiles', function (Blueprint $table) {
        $table->dropColumn(['birth_date', 'birth_place', 'gender']);
    });
}
};
