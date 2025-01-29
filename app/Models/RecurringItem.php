<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RecurringItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringItem extends Model
{
    /** @use HasFactory<RecurringItemFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'billing_date',
        'repeating_cadence',
        'description',
        'start_date',
        'end_date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function thenAction(): HasMany
    {
        return $this->hasMany(ThenAction::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }
}
