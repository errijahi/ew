<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SplitTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'payee',
        'notes',
        'team_id',
        'category_id',
        'tag_id',
    ];
}
