<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SplitTransaction extends Model
{
    protected $fillable = [
        'amount',
        'payee',
        'notes',
        'team_id',
        'category_id',
        'tag_id',
        'reviewed',
        'run_through_rules',
        'payee_id',
    ];

    public function thenAction(): BelongsTo
    {
        return $this->belongsTo(ThenAction::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function payee(): HasMany
    {
        return $this->hasMany(Payee::class);
    }

    public function category(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
