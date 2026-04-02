<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        
        // Relacionamentos
        $table->foreignId('institution_id')->constrained()->onDelete('cascade');
        $table->foreignId('plan_id')->constrained()->onDelete('restrict'); // Restrict impede deletar um plano se tiver alguém usando
        
        // Dados do Contrato mapeados no seu Figma
        $table->string('billing_cycle')->default('monthly'); // 'monthly' ou 'annual'
        $table->decimal('price', 10, 2); // O valor exato fechado na hora (caso o plano mude de preço depois, o do cliente se mantém)
        $table->string('status')->default('active'); // 'active', 'inactive', 'canceled', 'expiring'
        
        $table->date('expires_at')->nullable(); // Data em que o status muda para expirando/expirado
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
