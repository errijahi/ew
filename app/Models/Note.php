<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'filter',
        'if_action_id',
    ];

    protected $with = [
        'ifAction',
    ];

    public function ifAction()
    {
        return $this->hasMany(IfAction::class);
    }
}
