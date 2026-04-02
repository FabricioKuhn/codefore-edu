<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::table('classrooms', function (Blueprint $table) {
        $table->integer('total_lessons')->default(24); // Padrão 24
        $table->decimal('min_attendance_percent', 5, 2)->default(70.00); // Padrão 70%
    });
}
};
