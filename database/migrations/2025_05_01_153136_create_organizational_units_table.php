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
        Schema::create('organizational_units', function (Blueprint $table)
        {
            $table->id()->generatedAs()->always();
            $table->foreignId('organization_id')
                ->constrained()
                ->comment('Identificador de la organización a la que pertenece la unidad organizativa (o unidad administrativa).');
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->constrained()
                ->comment('Identificador de la unidad organizativa (o unidad administrativa) ascendente (origen/padre/madre).');
            $table->string('code')
                ->nullable()
                ->comment('Código que identifica de manera única a la unidad organizativa (o unidad administrativa).');
            $table->string('name')
                ->comment('Nombre oficial completo de la unidad organizativa (o unidad administrativa).');
            $table->string('acronym', 20)
                ->nullable()
                ->comment('Siglas o acrónimo del nombre de la unidad organizativa (o unidad administrativa).');
            $table->string('floor', 5)
                ->nullable()
                ->comment('
                  Ubicación espacial (en formato abreviado, por ejemplo: PA) de la unidad organizativa (o unidad administrativa) en la infraestructura física del ente u organización.'
                );
            $table->timestamps(6);
            $table->timestamp('disabled_at', 6)
                ->nullable()
                ->comment('Indica cuándo se marca la unidad organizativa (o unidad administrativa) como inactiva o desactivada.');
            $table->comment(
                'Registros de las unidades administrativas u organizativas que componen a la organización. ' .
                'Es el equivalente al organigrama institucional. Tabla reflexiva.'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};
