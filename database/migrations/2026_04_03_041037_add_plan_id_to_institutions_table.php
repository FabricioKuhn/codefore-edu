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
    Schema::table('institutions', function (Blueprint $table) {
        // Criamos a coluna plan_id como chave estrangeira
        // Usamos nullable para não quebrar as escolas que já existem
        $table->foreignId('plan_id')->nullable()->after('id')->constrained('plans')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('institutions', function (Blueprint $table) {
        $table->dropForeign(['plan_id']);
        $table->dropColumn('plan_id');
    });
}
};
