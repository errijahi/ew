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
        'reviewed',
        'run_through_rules',
        'payee_id',
    ];

    public function thenAction()
    {
        return $this->belongsTo(ThenAction::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function payee()
    {
        return $this->hasMany(Payee::class);
    }

    public function category()
    {
        return $this->hasMany(Category::class);
    }
}
