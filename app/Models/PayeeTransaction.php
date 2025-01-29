<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayeeTransaction extends Model
{
    protected $table = 'payee_transaction';

    protected $fillable = [
        'transaction_id',
        'payee_id',
    ];
}
