<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThenAction extends Model
{
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function recurringItem()
    {
        return $this->belongsTo(RecurringItem::class);
    }

    public function splitTransaction()
    {
        return $this->hasMany(SplitTransaction::class);
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
