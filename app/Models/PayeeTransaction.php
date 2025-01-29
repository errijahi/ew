<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeTransaction extends Model
{
    /** @use HasFactory<PayeeTransaction> */
    use HasFactory;

    protected $table = 'payee_transaction';

    protected $fillable = [
        'transaction_id',
        'payee_id',
    ];
}
