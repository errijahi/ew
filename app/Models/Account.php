<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'status',
        'name',
        'last_updated',
        'category_id',
        'user_id',
        'team_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function getStatusAttribute($value): bool
    {
        return $value === 'true';
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = $value ? 'true' : 'false';
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    public static function getMonthlyData($month, $year)
    {
        //        TODO: I will need to add by months and yeard and stuff because this will get all the data might be too much
        return Transaction::get();
    }
}
