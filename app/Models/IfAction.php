<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'matches_payee_name',
        'matches_category',
        'matches_notes',
        'matches_amount',
        'rules_id',
        'matches_day',
    ];
}
