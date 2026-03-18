<?php

namespace App\Models;

use Database\Factories\BoilerplateNpmPackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['package', 'dev', 'enabled'])]
class BoilerplateNpmPackage extends Model
{
    /** @use HasFactory<BoilerplateNpmPackageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dev' => 'boolean',
            'enabled' => 'boolean',
        ];
    }
}
