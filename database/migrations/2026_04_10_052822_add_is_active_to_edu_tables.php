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
  

    // Adiciona em activities após o campo due_date (o último antes dos timestamps)
    Schema::table('activities', function (Blueprint $table) {
        $table->boolean('is_active')->default(true)->after('end_date');
    });
}

public function down(): void
{
    Schema::table('lessons', fn($table) => $table->dropColumn('is_active'));
    Schema::table('activities', fn($table) => $table->dropColumn('is_active'));
}
};
