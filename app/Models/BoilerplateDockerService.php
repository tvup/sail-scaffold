<?php

namespace App\Models;

use Database\Factories\BoilerplateDockerServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'config', 'enabled'])]
class BoilerplateDockerService extends Model
{
    /** @use HasFactory<BoilerplateDockerServiceFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
