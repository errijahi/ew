<?php

declare(strict_types=1);

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
        return $this->hasMany(Transaction::class);
    }

    public function payeeFilter()
    {
        return $this->hasMany(PayeeFilter::class);
    }
}
