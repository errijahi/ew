<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filter',
    ];

    public function ifAction()
    {
        return $this->hasMany(IfAction::class);
    }
}
