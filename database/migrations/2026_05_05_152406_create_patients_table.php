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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // Añadimos campos
            $table->foreignID('user_id')
                ->constrained('users')
            // si lo borro, el paciente se borra también
                ->onDelete('cascade');

            $table->foreignID('blood_type_id')
                ->nullable()
                ->constrained('blood_types')
                ->onDelete('set null'); //si se borra el tipo de sangre, el paciente no se borra, pero su tipo de sangre se pone a null

            $table->string('allergies')
                ->nullable();

            $table->string('chronic_conditions')
                ->nullable();

            $table->string('surgical_history')
                ->nullable();

            $table->string('family_history')
                ->nullable();

            $table->string('observations')
                ->nullable();

            $table->string('emergency_contact_name')
                ->nullable();

            $table->string('emergency_contact_phone')
                ->nullable();

            $table->string('emergency_contact_relationship')
                ->nullable();
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
