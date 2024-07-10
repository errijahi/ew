<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfAction extends Model
{
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

    public function note()
    {
        return $this->belongsTo(Note::class, 'matches_notes');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'matches_category');
    }

    public function day()
    {
        return $this->belongsTo(Day::class, 'matches_day');
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class, 'matches_amount');
    }

    public function payee()
    {
        return $this->belongsTo(PayeeName::class, 'matches_payee_name');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'in_account');
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
