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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            // Informations personnelles
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->enum('gender', ['M', 'F']);
            $table->string('nationality')->default('Camerounaise');
            $table->text('address')->nullable();
            
            // Parcours académique précédent
            $table->string('previous_school')->nullable(); // Nom du lycée ou université précédente
            $table->string('previous_diploma')->nullable(); // ex: BAC A4, Licence
            $table->year('graduation_year')->nullable();
            
            // Choix de formation dans SHARKLINK
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            
            // Statut du dossier
            $table->enum('status', ['BROUILLON', 'SOUMIS', 'EN_COURS', 'ACCEPTE', 'REJETE', 'INSCRIT'])->default('BROUILLON');
            $table->text('admin_comment')->nullable(); // Pour les remarques de l'administration
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
