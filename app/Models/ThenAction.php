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
        'set_category',
        'set_uncategorized',
        'add_tag',
        'delete_transaction',
        'link_to_recurring_item',
        'do_not_link_to_recurring_item',
        'do_not_create_rule',
        'split_transaction',
        'mark_as_reviewed',
        'mark_as_unreviewed',
        'send_me_email',
        'rule_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'set_category');
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'add_tag');
    }

    public function recurringItem()
    {
        return $this->belongsTo(RecurringItem::class, 'link_to_recurring_item');
    }

    public function splitTransaction()
    {
        return $this->belongsTo(SplitTransaction::class, 'split_transaction');
    }
}
