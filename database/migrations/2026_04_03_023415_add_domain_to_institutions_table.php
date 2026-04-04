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
            // Adiciona a coluna domain. 
            // Ela deve ser unique porque dois clientes não podem ter a mesma URL.
            // É nullable caso você queira criar uma instituição sem domínio definido ainda.
            $table->string('domain')->unique()->nullable()->after('cnpj');
            
            // Aproveitando a migration: se você ainda não tem a coluna de status (boolean)
            // que usamos no bloqueio, garanta que ela existe aqui.
            if (!Schema::hasColumn('institutions', 'status')) {
                $table->boolean('status')->default(true)->after('domain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['domain', 'status']);
        });
    }
};