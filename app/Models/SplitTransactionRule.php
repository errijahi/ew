<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplitTransactionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'split_transaction_id',
        'rule_id',
    ];
}
