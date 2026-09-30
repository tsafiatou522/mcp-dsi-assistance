<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['direction_id']);
            $table->dropColumn(['role', 'direction_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['agent', 'technicien', 'responsable', 'admin'])->default('agent');
            $table->foreignId('direction_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['direction_id']);
            $table->dropColumn(['role', 'direction_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['agent', 'technicien', 'responsable', 'admin'])->default('agent');
            $table->foreignId('direction_id')->constrained()->onDelete('cascade');
        });
    }
};