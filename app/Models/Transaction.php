<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'amount',
        'page',
        'date',
        'status',
        'transaction_source',
        'team_id',
    ];

    protected $with = ['payee'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function recurring()
    {
//        dd('test');

        return $this->hasMany(TransactionRecurringItem::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }

    public function payee()
    {
        return $this->belongsToMany(Payee::class, 'transaction_payees', 'payee_id','transaction_id');
    }

//    public function tag()
//    {
//        return $this->belongsTo(Tag::class);
//    }
}
