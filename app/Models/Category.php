<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function subCategoryGroup()
    {
        return $this->hasMany(Category::class, 'sub_category_group_id', 'id');
    }

    public function parentCategoryGroup()
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_group_id');
    }

    public function team()
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringItems()
    {
        return $this->belongsToMany(RecurringItem::class);
    }

    public function splitTransactions()
    {
        return $this->hasMany(SplitTransaction::class);
    }

    public function ifActions()
    {
        return $this->hasMany(IfAction::class);
    }

    public function thenActions()
    {
        return $this->hasMany(ThenAction::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
