<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleSplitTransaction extends Model
{
    use HasFactory;

    protected $table = 'rule_split_transaction';

    protected $fillable = [
        'transaction_id',
        'rule_id',
    ];

    public function thenAction()
    {
        return $this->belongsTo(ThenAction::class);
    }
}
