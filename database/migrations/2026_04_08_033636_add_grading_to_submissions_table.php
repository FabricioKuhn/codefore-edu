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
    Schema::table('submissions', function (Blueprint $table) {
        // Adiciona os campos de correção que estão faltando
        $table->text('feedback')->nullable()->after('status');
        $table->json('teacher_notes')->nullable()->after('feedback');
        $table->timestamp('evaluated_at')->nullable()->after('teacher_notes');
    });
}

public function down(): void
{
    Schema::table('submissions', function (Blueprint $table) {
        $table->dropColumn(['feedback', 'teacher_notes', 'evaluated_at']);
    });
}
};
