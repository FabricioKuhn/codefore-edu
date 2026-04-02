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
        $table->date('start_date')->nullable(); // Data de Início
        $table->string('frequency')->default('weekly'); // daily, weekly, biweekly, custom
        $table->json('days_of_week')->nullable(); // [1, 3, 5] (Seg, Qua, Sex)
        $table->time('start_time')->nullable();
        $table->time('end_time')->nullable();
        $table->boolean('skip_holidays')->default(true);
    });
}
};
