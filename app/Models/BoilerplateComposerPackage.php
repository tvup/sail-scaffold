<?php

namespace App\Models;

use Database\Factories\BoilerplateComposerPackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['package', 'dev', 'enabled'])]
class BoilerplateComposerPackage extends Model
{
    /** @use HasFactory<BoilerplateComposerPackageFactory> */
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
