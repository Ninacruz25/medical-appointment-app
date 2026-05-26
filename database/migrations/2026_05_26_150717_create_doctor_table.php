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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignID('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignID('specialty_id')
                ->nullable()
                ->constrained('specialties')
                ->onDelete('set null');

            $table->string('medical_license_number')
                ->nullable();

            $table->string('biography')
                ->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
