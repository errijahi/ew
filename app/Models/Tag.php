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

    public static function getMonthlyData($start = null, $end = null)
    {

        return Transaction::get();

    }

    public function transaction(){
        return $this->hasMany(Transaction::class);
    }
}
