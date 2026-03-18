<?php

namespace Database\Seeders;

use App\Models\BoilerplateSailService;
use Illuminate\Database\Seeder;

class BoilerplateSailServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'mysql' => true,
            'pgsql' => false,
            'mariadb' => false,
            'redis' => true,
            'memcached' => false,
            'meilisearch' => false,
            'typesense' => false,
            'minio' => false,
            'mailpit' => true,
            'selenium' => false,
            'soketi' => false,
            'valkey' => false,
        ];

        foreach ($services as $name => $enabled) {
            BoilerplateSailService::query()->firstOrCreate(
                ['name' => $name],
                ['enabled' => $enabled],
            );
        }
    }
}
