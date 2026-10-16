<?php

namespace Database\Factories;

use App\Models\CodeList;
use App\Models\CodeListItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CodeListItem>
 */
class CodeListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_list_id' => CodeList::factory(),
            'code' => fake()->unique()->bothify('CODE-###'),
            'label' => fake()->words(3, true),
            'sort_order' => fake()->numberBetween(0, 100),
            'active' => fake()->boolean(85),
        ];
    }
}
