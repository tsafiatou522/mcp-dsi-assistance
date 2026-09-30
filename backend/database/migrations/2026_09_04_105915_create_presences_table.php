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
        Schema::create('presences', function (Blueprint $table) {
            $table->id(); $table->foreignId('technicien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('direction_id')->constrained('directions')->onDelete('cascade');
            $table->timestamp('date_arrivee')->useCurrent();
            $table->timestamp('date_depart')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
