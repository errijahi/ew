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
        return $this->belongsTo(Note::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class);
    }

    public function payee()
    {
        return $this->belongsTo(PayeeName::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
