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
        Schema::create('organizational_unit_user', function (Blueprint $table)
        {
            $table->foreignId('organizational_unit_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Identificador de la unidad organizativa (administrativa).');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->comment('Identificador del usuario.');
            $table->primary(['organizational_unit_id', 'user_id']);
            $table->timestamps(6);
            $table->timestamp('disabled_at', 6)
                ->nullable()
                ->comment('Indica cuándo se marca el registro como inactivo o desactivado.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_unit_user');
    }
};
