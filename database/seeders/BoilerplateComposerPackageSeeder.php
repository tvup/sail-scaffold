<?php

namespace Database\Seeders;

use App\Models\BoilerplateComposerPackage;
use Illuminate\Database\Seeder;

class BoilerplateComposerPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            ['package' => 'laravel/pail', 'dev' => true],
            ['package' => 'laravel/pint', 'dev' => true],
        ];

        foreach ($packages as $pkg) {
            BoilerplateComposerPackage::query()->firstOrCreate(
                ['package' => $pkg['package']],
                ['dev' => $pkg['dev'], 'enabled' => true],
            );
        }
    }
}
