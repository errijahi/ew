<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    /** @return HasMany<Account,$this> */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
