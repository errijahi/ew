<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IfAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payee_filter_id',
        'note_id',
        'amount_id',
        'day_id',
        'rule_id',
        'category_id',
        'account_id',
    ];

    protected $with = [
        'category',
        'amount',
        'day',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function amount()
    {
        return $this->belongsTo(Amount::class);
    }

    public function payee()
    {
        return $this->belongsTo(PayeeFilter::class, 'payee_filter_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(static function ($model): void {

            if ($model->payee_filter_id !== null) {
                PayeeFilter::destroy($model->payee_filter_id);
            } elseif ($model->note_id !== null) {
                Note::destroy($model->note_id);
            } elseif ($model->amount_id !== null) {
                Amount::destroy($model->amount_id);
            } elseif ($model->day_id) {
                Day::destroy($model->day_id);
            }
        });
    }
}
