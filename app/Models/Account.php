<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
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

    public function getStatusAttribute(string $value): bool
    {
        return $value === 'true';
    }

    public function setStatusAttribute(string $value): void
    {
        $this->attributes['status'] = $value ? 'true' : 'false';
    }

    /** @return HasOne<Category, $this> */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<Transaction, $this> */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /** @return BelongsTo<AccountType, $this> */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /** @return HasMany<IfAction, $this> */
    public function ifAction(): HasMany
    {
        return $this->hasMany(IfAction::class);
    }

    /**
     * @return Collection<int, Transaction>
     */
    public static function getMonthlyData(string $month, string $year): Collection
    {
        // TODO: I will need to add by months and yeard and stuff because this will get all the data might be too much
        return Transaction::get();
    }
}
