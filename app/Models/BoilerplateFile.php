<?php

namespace App\Models;

use Database\Factories\BoilerplateFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'content', 'enabled'])]
class BoilerplateFile extends Model
{
    /** @use HasFactory<BoilerplateFileFactory> */
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
