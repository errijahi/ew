<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PayeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payee extends Model
{
    /** @use HasFactory<PayeeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<PayeeFilter, $this>
     */
    public function payeeFilter(): HasMany
    {
        return $this->hasMany(PayeeFilter::class);
    }
}
