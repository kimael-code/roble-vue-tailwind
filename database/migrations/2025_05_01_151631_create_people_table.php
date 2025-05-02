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
        Schema::create('people', function (Blueprint $table) {
            $table->id()->generatedAs()->always();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('id_card', 8)
                ->unique()
                ->comment('Número de la cédula de identidad.');
            $table->string('names')
                ->comment('Nombres de la persona (primero y/o segundo).');
            $table->string('surnames')
                ->comment('Apellidos de la persona (primero y/o segundo).');
            $table->jsonb('phones')
                ->nullable()
                ->comment('Números o extensiones telefónicas de la persona.');
            $table->jsonb('emails')
                ->nullable()
                ->comment('Correos electrónicos.');
            $table->string('position')
                ->nullable()
                ->comment('Denominación del cargo de la persona.');
            $table->string('staff_type')
                ->nullable()
                ->comment('Denominación del tipo de personal.');
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
