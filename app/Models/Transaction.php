<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'amount',
        'page',
        'date',
        'status',
        'transaction_source',
        'payee',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
