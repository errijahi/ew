<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'filter',
        'if_action_id',
    ];

    protected $with = [
        'ifAction',
    ];

    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }
}
