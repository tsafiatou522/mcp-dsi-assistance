<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['ordinateur', 'imprimante', 'scanner', 'reseau', 'autre'])->default('autre');
            $table->string('numero_serie')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->foreignId('direction_id')->constrained('directions')->onDelete('cascade');
            $table->foreignId('bureau_id')->nullable()->constrained('bureaux')->onDelete('set null');
            $table->enum('etat', ['fonctionnel', 'en_panne', 'en_maintenance', 'hors_service'])->default('fonctionnel');
            $table->date('date_acquisition')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
