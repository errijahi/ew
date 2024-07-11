<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'payee_id',
        'notes',
        'amount',
        'status',
        'transaction_source',
        'team_id',
    ];

    protected $with = ['payee'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recurringItem()
    {
        return $this->belongsTo(RecurringItem::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
