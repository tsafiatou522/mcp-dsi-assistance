<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colonnes déjà présentes dans create_categories_table — migration désactivée.
    }

    public function down(): void
    {
        // Rien à annuler.
    }
};
