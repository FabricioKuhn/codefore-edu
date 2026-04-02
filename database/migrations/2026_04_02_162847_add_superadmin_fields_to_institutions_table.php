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
    Schema::table('institutions', function (Blueprint $table) {
        // Dados Cadastrais
        $table->string('trading_name')->nullable(); // Nome Fantasia
        $table->string('company_name')->nullable(); // Razão Social
        $table->string('cnpj')->unique()->nullable();
        $table->string('tax_indicator')->nullable(); // Indicador IE
        $table->string('state_registration')->nullable();
        $table->string('municipal_registration')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        
        // Endereço
        $table->string('zip_code')->nullable(); // CEP
        $table->string('address')->nullable();
        $table->string('address_number')->nullable();
        $table->string('neighborhood')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        
        // White Label - Cores (Valores default limpos do CodeForce)
        $table->string('primary_color')->default('#10B981'); // Ex: Verde
        $table->string('secondary_color')->default('#1F2937'); // Ex: Cinza escuro
        $table->string('tertiary_color')->default('#F3F4F6'); // Ex: Cinza claro
        
        // White Label - Logos
        $table->string('logo_original')->nullable();
        $table->string('logo_negative')->nullable();
        $table->string('flat_icon')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            //
        });
    }
};
