<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IfActionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IfAction extends Model
{
    /** @use HasFactory<IfActionFactory> */
    use HasFactory;

    protected $fillable = [
        'payee_filter_id',
        'note_id',
        'amount_id',
        'day_id',
        'rule_id',
        'category_id',
        'account_id',
    ];

    protected $with = [
        'category',
        'amount',
        'day',
    ];

    /** @return BelongsTo<Note,$this> */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    /** @return BelongsTo<Category,$this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /** @return BelongsTo<Day,$this> */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /** @return BelongsTo<Amount,$this> */
    public function amount(): BelongsTo
    {
        return $this->belongsTo(Amount::class);
    }

    /** @return BelongsTo<Payee, $this> */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(PayeeFilter::class, 'payee_filter_id');
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** @return BelongsTo<Rule, $this> */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($model): void {

            if ($model->payee_filter_id !== null) {
                PayeeFilter::destroy($model->payee_filter_id);
            } elseif ($model->note_id !== null) {
                Note::destroy($model->note_id);
            } elseif ($model->amount_id !== null) {
                Amount::destroy($model->amount_id);
            } elseif ($model->day_id) {
                Day::destroy($model->day_id);
            }
        });
    }
}
