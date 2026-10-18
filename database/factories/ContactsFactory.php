<?php

namespace Database\Factories;

use App\Models\Contacts;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Contacts>
 */
class ContactsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->firstName();
        $middleName = fake()->firstName();
        $lastName = fake()->lastName();
        $taxIdNumber = fake()->unique()->numerify('########');
        $controlLetters = 'TRWAGMYFPDXBNJZSQVHLCKE';

        return [
            'id' => (string) Str::uuid(),
            'code' => strtoupper(fake()->unique()->bothify('CONTACT_###??')),
            'type' => 'Persona',
            'taxId' => $taxIdNumber.$controlLetters[(int) $taxIdNumber % 23],
            'formatedName' => "{$name} {$middleName} {$lastName}",
            'name' => $name,
            'middleName' => $middleName,
            'lastName' => $lastName,
            'companyName' => null,
            'gender' => fake()->randomElement(['Hombre', 'Mujer', 'Other']),
            'birthDate' => fake()->date(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobilePhone' => fake()->phoneNumber(),
        ];
    }
}
