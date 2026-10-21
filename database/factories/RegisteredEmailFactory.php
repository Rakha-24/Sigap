<?php

namespace Database\Factories;

use App\Models\RegisteredEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegisteredEmail>
 */
class RegisteredEmailFactory extends Factory
{
    protected $model = RegisteredEmail::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'nama' => fake()->name(),
            'is_active' => true,
        ];
    }
}
