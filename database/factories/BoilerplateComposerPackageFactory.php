<?php

namespace Database\Factories;

use App\Models\BoilerplateComposerPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateComposerPackage>
 */
class BoilerplateComposerPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package' => fake()->word().'/'.fake()->word(),
            'dev' => false,
            'enabled' => true,
        ];
    }
}
