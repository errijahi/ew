<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function transaction()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_payees');
    }
}
