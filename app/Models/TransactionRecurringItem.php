<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionRecurringItem extends Model
{
    protected $table = 'transaction_recurring_item';

    protected $fillable = [
        'transaction_id',
        'recurring_item_id',
    ];
}
