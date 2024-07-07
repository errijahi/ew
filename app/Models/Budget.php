<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget',
    ];

    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
