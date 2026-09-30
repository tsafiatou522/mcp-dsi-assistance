<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('demandes');
        Schema::enableForeignKeyConstraints();

        Schema::create('demandes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('technicien_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('direction_id')
                ->constrained('directions')
                ->onDelete('cascade');

            $table->foreignId('bureau_id')
                ->constrained('bureaux')
                ->onDelete('cascade');

            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');

            $table->text('description');

            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');

            $table->enum('statut', [
                'nouvelle',
                'en_attente',
                'prise_en_charge',
                'en_intervention',
                'resolue',
                'en_attente_confirmation',
                'cloturee',
            ])->default('nouvelle');

            $table->string('piece_jointe')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes');
    }
};
