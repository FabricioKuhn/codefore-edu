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
    Schema::create('lesson_materials', function (Blueprint $table) {
        $table->id();
        $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
        $table->string('type'); // 'file' ou 'link'
        $table->string('title');
        $table->string('path_or_url'); // Caminho do storage ou link externo
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('lesson_materials');
}
};
