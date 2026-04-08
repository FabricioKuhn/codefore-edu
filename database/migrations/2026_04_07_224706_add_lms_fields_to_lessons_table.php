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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('type')->default('standard')->after('id');
            
            // 🌟 ADICIONAMOS O TÍTULO AQUI
            $table->string('title')->nullable()->after('type'); 
            
            $table->text('description')->nullable()->after('title'); 
            $table->string('video_url')->nullable();
            $table->string('main_material_path')->nullable();
            $table->integer('xp_reward')->default(0);
        });
    }

public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['type', 'title', 'description', 'video_url', 'main_material_path', 'xp_reward']);
        });
    }
};
