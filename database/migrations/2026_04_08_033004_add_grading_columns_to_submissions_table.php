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
        $table->text('feedback')->nullable()->after('earned_coins');
        $table->json('teacher_notes')->nullable()->after('feedback');
        $table->timestamp('evaluated_at')->nullable()->after('teacher_notes');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            //
        });
    }
};
