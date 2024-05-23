<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeName extends Model
{
    use HasFactory;

    protected $fillable = [
        'payee_name',
        'filter',
    ];
}
