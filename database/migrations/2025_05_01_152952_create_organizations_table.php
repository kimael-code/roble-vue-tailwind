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
        Schema::create('organizations', function (Blueprint $table)
        {
            $table->id()->generatedAs()->always();
            $table->string('rif', 12)
                ->unique()
                ->comment('Registro de Información Fiscal.');
            $table->string('name')
                ->comment('Razón social de la organización.');
            $table->string('logo_path')
                ->nullable()
                ->comment('Logotipo de la organización. Es usado para generar documentos que requieran el logotipo.');
            $table->string('acronym', 20)
                ->nullable()
                ->comment('Siglas o acrónimo de la organización.');
            $table->text('address')
                ->nullable()
                ->comment('Dirección de la organización.');
            $table->timestamps(6);
            $table->timestamp('disabled_at', 6)
                ->nullable()
                ->comment('Indica cuándo se marca la organización como inactiva o desactivada.');
            $table->comment(
                'Registros de los datos de la organización (compañía, ente u organismo). '.
                'Técnicamente pueden existir múltiples organizaciones registradas pero la aplicación o sistema '.
                'está diseñada para trabajar solamente con una organización activa.'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
