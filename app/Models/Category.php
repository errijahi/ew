<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'treat_as_income',
        'exclude_from_budget',
        'exclude_from_total',
        'account_id',
        'sub_category_group_id',
    ];

    public function subCategoryGroup()
    {
        return $this->hasMany(Category::class, 'sub_category_group_id', 'id');
    }

    public function parentCategoryGroup()
    {
        return $this->hasOne(Category::class, 'id', 'sub_category_group_id');
    }
}
