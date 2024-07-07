<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function SplitTransactions()
    {
        return $this->hasMany(SplitTransaction::class);
    }

    public function thenAction()
    {
        return $this->hasMany(ThenAction::class);
    }

    public static function getMonthlyData()
    {
        return Transaction::get();
    }
}
