<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryRecurringItem extends Model
{
    use HasFactory;

    protected $table = 'category_recurring_item';
}
