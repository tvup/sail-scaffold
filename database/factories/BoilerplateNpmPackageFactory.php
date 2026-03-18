<?php

namespace Database\Factories;

use App\Models\BoilerplateNpmPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateNpmPackage>
 */
class BoilerplateNpmPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package' => fake()->word(),
            'dev' => false,
            'enabled' => true,
        ];
    }
}
