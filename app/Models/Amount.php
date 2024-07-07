<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'filter',
    ];

    public function ifAction()
    {
        return $this->hasMany(IfAction::class);
    }
}
