<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $institution = Institution::create([
            'name' => 'CodeForce',
            'slug' => 'codeforce',
            'status' => true,
        ]);

        User::create([
            'institution_id' => $institution->id,
            'name' => 'Admin CodeForce',
            'username' => 'admin',
            'email' => 'admin@codeforce.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'status' => true,
        ]);
    }
}
