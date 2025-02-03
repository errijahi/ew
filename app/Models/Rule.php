<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends Model
{
    /** @use HasFactory<RuleFactory> */
    use HasFactory;

    protected $fillable = [
        'priority',
        'stop_processing_other_rules',
        'delete_this_rule_after_use',
        'rule_on_transaction_update',
        'team_id',
    ];

    protected $with = [
        'ifAction',
        'thenAction',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<IfAction, $this> */
    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    /** @return HasMany<ThenAction, $this> */
    public function thenAction(): HasMany
    {
        return $this->hasMany(ThenAction::class);
    }

    /** @return hasMany<SplitTransaction, $this> */
    public function splitTransactions(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }
}
