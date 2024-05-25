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
}
