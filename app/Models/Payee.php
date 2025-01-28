<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payeeFilter(): HasMany
    {
        return $this->hasMany(PayeeFilter::class);
    }
}
