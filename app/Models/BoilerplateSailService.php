<?php

namespace App\Models;

use Database\Factories\BoilerplateSailServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'config', 'enabled'])]
class BoilerplateSailService extends Model
{
    /** @use HasFactory<BoilerplateSailServiceFactory> */
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
