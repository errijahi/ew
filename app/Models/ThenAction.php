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

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Payee, $this>*/
    public function payee(): BelongsTo
    {
        return $this->belongsTo(Payee::class);
    }

    /** @return BelongsTo<Tag, $this> */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /** @return BelongsTo<RecurringItem, $this> */
    public function recurringItem(): BelongsTo
    {
        return $this->belongsTo(RecurringItem::class);
    }

    /** @return HasMany<SplitTransaction, $this>*/
    public function splitTransaction(): HasMany
    {
        return $this->hasMany(SplitTransaction::class);
    }

    /** @return BelongsTo<Rule, $this>*/
    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}
