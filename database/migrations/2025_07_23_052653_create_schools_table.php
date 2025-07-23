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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('npsn')->unique();
            $table->string('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('education_type')->nullable(); // SMA, SMK, etc.
            $table->string('school_type')->nullable(); // swasta or negeri
            $table->string('principal_name')->nullable();
            $table->string('principal_nip')->nullable();
            $table->bigInteger('student_count')->default(0);
            $table->bigInteger('teacher_count')->default(0);
            $table->string('practice_facility')->nullable(); // e.g., "Laboratorium IPA, Laboratorium Komputer"
            $table->string('sports_facility')->nullable(); // e.g., "Lapangan Sepak Bola, Lapangan Basket"
            $table->string('curriculum')->nullable(); // e.g., "Kurikulum 2013, Kurikulum Merdeka"
            $table->string('extracurricular')->nullable(); // e.g., "Pramuka, Paskibra, OSIS"
            // prestasi
            $table->text('achievements')->nullable(); // e.g., "Juara 1 Lomba Cerdas Cermat, Juara 2 Olimpiade Sains"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
