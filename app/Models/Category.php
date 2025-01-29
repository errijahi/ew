<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
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

    /**
     * @return HasMany<Category, $this>
     */
    public function subCategoryGroup(): HasMany
    {
        return $this->hasMany(Category::class, 'sub_category_group_id', 'id');
    }

    /**
     * @return HasOne<Category, $this>
     */
    public function parentCategoryGroup(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_group_id');
    }

    /**
     * @return BelongsTo<Team, $this>
     */
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

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return BelongsToMany<RecurringItem, $this>
     */
    public function recurringItems(): BelongsToMany
    {
        return $this->belongsToMany(RecurringItem::class);
    }

    /**
     * @return HasMany<SplitTransaction, $this>
     */
    public function splitTransactions(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }

    /**
     * @return HasMany<IfAction, $this>
     */
    public function ifActions(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    /**
     * @return HasMany<ThenAction, $this>
     */
    public function thenActions(): HasMany
    {
        return $this->hasMany(ThenAction::class);
    }

    /**
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
