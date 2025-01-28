<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayeeFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'filter',
        'payee_id',
    ];

    protected $with = [
        'payeeName',
    ];

    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    public function payeeName(): BelongsTo
    {
        return $this->belongsTo(Payee::class, 'payee_id');
    }
}
