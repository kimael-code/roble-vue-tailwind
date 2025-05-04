<?php

namespace Database\Factories\Organization;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rif' => fake()->taxpayerIdentificationNumber('-'),
            'name' => fake()->company(),
            'logo_path' => '',
            'acronym' => fake()->companySuffix(),
            'address' => fake()->address(),
        ];
    }
}
