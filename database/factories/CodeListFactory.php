<?php

namespace Database\Factories;

use App\Models\CodeList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CodeList>
 */
class CodeListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
        ];
    }
}
