<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Criamos um sufixo aleatório para garantir que NADA se repita
        $uniqueSuffix = '_' . Str::random(6); 

        return [
            'name' => fake()->name(),
            
            // ADICIONAMOS O USERNAME AQUI (Causa do erro atual)
            'username' => strtolower(fake()->userName() . $uniqueSuffix), 
            'email' => strtolower(fake()->userName() . $uniqueSuffix . '@example.com'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}