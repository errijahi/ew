<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ThenActionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThenAction extends Model
{
    /** @use HasFactory<ThenActionFactory> */
    use HasFactory;

    protected $fillable = [
        'set_payee',
        'set_notes',
        'category_id',
        'set_uncategorized',
        'tag_id',
        'delete_transaction',
        'recurring_item_id',
        'do_not_link_to_recurring_item',
        'do_not_create_rule',
        'rule_split_transaction_id',
        'reviewed',
        'send_me_email',
    ];

    protected $with = [
        'splitTransaction',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(Payee::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function recurringItem(): BelongsTo
    {
        return $this->belongsTo(RecurringItem::class);
    }

    public function splitTransaction(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
