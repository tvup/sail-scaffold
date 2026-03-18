<?php

namespace Database\Seeders;

use App\Models\BoilerplateNpmPackage;
use Illuminate\Database\Seeder;

class BoilerplateNpmPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            ['package' => 'tailwindcss@^4.0', 'dev' => true],
            ['package' => '@tailwindcss/vite', 'dev' => true],
            ['package' => 'laravel-vite-plugin', 'dev' => true],
            ['package' => 'axios', 'dev' => true],
            ['package' => 'concurrently', 'dev' => true],
        ];

        foreach ($packages as $pkg) {
            BoilerplateNpmPackage::query()->firstOrCreate(
                ['package' => $pkg['package']],
                ['dev' => $pkg['dev'], 'enabled' => true],
            );
        }
    }
}
