<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'filter',
    ];

    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }
}
