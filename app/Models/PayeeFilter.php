<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'filter',
        'payee_id',
    ];

    protected $with = [
        'payeeName',
    ];

    public function ifAction()
    {
        return $this->hasMany(IfAction::class);
    }

    public function payeeName()
    {
        return $this->belongsTo(Payee::class, 'payee_id');
    }
}
