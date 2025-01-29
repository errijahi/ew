<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $fillable = [
        'day',
        'filter',
    ];

    /**
     * @return HasMany<IfAction,$this>
     */
    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }
}
