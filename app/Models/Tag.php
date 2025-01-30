<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<Transaction, $this> */
    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** @return HasMany<SplitTransaction, $this> */
    public function SplitTransactions(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }

    /** @return HasMany<ThenAction, $this> */
    public function thenAction(): HasMany
    {
        return $this->hasMany(ThenAction::class);
    }

    public static function getMonthlyData()
    {
        return Transaction::get();
    }
}
