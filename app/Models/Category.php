<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'budget',
        'description',
        'treat_as_income',
        'exclude_from_budget',
        'exclude_from_total',
        'account_id',
        'sub_category_group_id',
        'team_id',
    ];

    protected $with = ['budgets'];

    public function subCategoryGroup(): HasMany
    {
        return $this->hasMany(Category::class, 'sub_category_group_id', 'id');
    }

    public function parentCategoryGroup(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_group_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }

    public static function getTableValues()
    {
        return self::get()->keyBy('id')->toArray();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringItems(): BelongsToMany
    {
        return $this->belongsToMany(RecurringItem::class);
    }

    public function splitTransactions(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }

    public function ifActions(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    public function thenActions(): HasMany
    {
        return $this->hasMany(ThenAction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
