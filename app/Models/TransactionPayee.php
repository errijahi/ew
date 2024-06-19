<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayee extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'payee_id',
    ];

    //    public function payee()
    //    {
    //        return $this->hasMany();
    //    }
}
