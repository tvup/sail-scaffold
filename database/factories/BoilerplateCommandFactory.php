<?php

namespace Database\Factories;

use App\Models\BoilerplateCommand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateCommand>
 */
class BoilerplateCommandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'command' => fake()->word().'_'.fake()->word(),
            'sort_order' => 0,
            'enabled' => true,
        ];
    }
}
