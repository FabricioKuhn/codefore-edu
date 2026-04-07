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
    Schema::table('questions', function (Blueprint $table) {
        // Adicionando as colunas que estão faltando
        $table->string('external_link')->nullable()->after('guidelines');
        $table->string('external_link_label')->nullable()->after('external_link');
        
        // Caso você ainda não tenha a coluna de anexos (pelo erro parece que já tem, mas por segurança):
        if (!Schema::hasColumn('questions', 'attachments')) {
            $table->json('attachments')->nullable()->after('statement');
        }
    });
}

public function down(): void
{
    Schema::table('questions', function (Blueprint $table) {
        $table->dropColumn(['external_link', 'external_link_label', 'attachments']);
    });
}
};
