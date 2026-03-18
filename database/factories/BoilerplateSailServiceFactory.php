<?php

namespace Database\Factories;

use App\Models\BoilerplateSailService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoilerplateSailService>
 */
class BoilerplateSailServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['mysql', 'pgsql', 'mariadb', 'redis', 'memcached', 'meilisearch', 'typesense', 'minio', 'mailpit', 'selenium', 'soketi', 'valkey']),
            'config' => null,
            'enabled' => true,
        ];
    }
}
