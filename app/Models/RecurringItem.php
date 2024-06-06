<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'billing_date',
        'repeating_cadence',
        'description',
        'start_date',
        'end_date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function getMonthlyData($month, $year)
    {
        //        TODO: I will need to add by months and yeard and stuff because this will get all the data might be too much
        return Transaction::get();
    }
}
