<?php

namespace App\Models;

use Database\Factories\BoilerplateCommandFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'command', 'sort_order', 'enabled'])]
class BoilerplateCommand extends Model
{
    /** @use HasFactory<BoilerplateCommandFactory> */
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
