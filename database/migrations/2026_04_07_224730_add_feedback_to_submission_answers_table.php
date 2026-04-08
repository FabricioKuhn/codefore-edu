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
        Schema::table('submission_answers', function (Blueprint $table) {
            // Removi o ->after() para o MySQL colocar no final da tabela sem reclamar
            $table->string('status')->default('pending'); // pending ou graded
            $table->text('feedback')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('submission_answers', function (Blueprint $table) {
            $table->dropColumn(['status', 'feedback']);
        });
    }
};
