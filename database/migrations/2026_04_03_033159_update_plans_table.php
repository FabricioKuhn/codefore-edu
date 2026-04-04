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
    Schema::table('plans', function (Blueprint $table) {
        // Adiciona o que falta
        if (!Schema::hasColumn('plans', 'slug')) {
            $table->string('slug')->unique()->after('name');
        }
        if (!Schema::hasColumn('plans', 'is_free')) {
            $table->boolean('is_free')->default(false)->after('annual_price');
        }

        // Renomeia os campos para bater com o padrão do Controller/View que fizemos
        // Se o seu Laravel for o 10, 11 ou 12, isso funciona nativamente
        $table->renameColumn('monthly_price', 'price_monthly');
        $table->renameColumn('annual_price', 'price_yearly');
        $table->renameColumn('classroom_limit', 'limit_classes');
        $table->renameColumn('student_limit', 'limit_students_per_class');
        $table->renameColumn('task_limit', 'limit_tasks_per_class');
    });
}

public function down(): void
{
    Schema::table('plans', function (Blueprint $table) {
        $table->dropColumn(['slug', 'is_free']);
        // O rollback de rename é opcional em dev, mas pode ser feito se precisar
    });
}
};
