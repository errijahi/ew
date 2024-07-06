<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'status',
        'account_name',
        'last_updated',
        'category_id',
        'user_id',
        'team_id',
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public static function getMonthlyData($month, $year)
    {
        //        TODO: I will need to add by months and yeard and stuff because this will get all the data might be too much
        return Transaction::get();
    }
}
