<?php

namespace Database\Factories;

use App\Models\BoilerplateDockerService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateDockerService>
 */
class BoilerplateDockerServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'config' => "image: ".fake()->word().":latest\nports:\n  - '8080:80'",
            'enabled' => true,
        ];
    }
}
