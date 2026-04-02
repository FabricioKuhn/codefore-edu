<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_name')) {
                $table->string('guardian_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'guardian_phone')) {
                $table->string('guardian_phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'street')) {
                $table->string('street')->nullable();
            }
            if (!Schema::hasColumn('users', 'neighborhood')) {
                $table->string('neighborhood')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('users', 'complement')) {
                $table->string('complement')->nullable();
            }
            if (!Schema::hasColumn('users', 'documents')) {
                $table->json('documents')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cpf', 'phone', 'guardian_name', 'guardian_phone', 'birth_date',
                'zip_code', 'street', 'neighborhood', 'city', 'state', 'complement',
                'documents', 'is_active'
            ]);
        });
    }
};