<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRecurringItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'recurring_item_id',
    ];
}
