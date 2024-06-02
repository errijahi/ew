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

    public function getMonthlyData($month, $year)
    {
//        TODO: I will need to add by months and yeard and stuff because this will get all the data might be too much
        return  Transaction::get();
    }

}
