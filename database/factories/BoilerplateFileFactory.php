<?php

namespace Database\Factories;

use App\Models\BoilerplateFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateFile>
 */
class BoilerplateFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->word().'/'.fake()->word().'.txt',
            'content' => fake()->paragraph(),
            'enabled' => true,
        ];
    }
}
