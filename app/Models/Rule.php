<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'priority',
        'stop_processing_other_rules',
        'delete_this_rule_after_use',
        'rule_on_transaction_update',
        'team_id',
    ];

    protected $with = [
        'ifAction',
        'thenAction',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function ifAction()
    {
        return $this->hasMany(IfAction::class);
    }

    public function thenAction()
    {
        return $this->hasMany(ThenAction::class);
    }

    public function splitTransactions()
    {
        return $this->hasMany(SplitTransaction::class);
    }
}
