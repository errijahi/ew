<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getMonthlyData($month, $year)
    {
        $tagIds = $this->get()->pluck('id');
        $transactionAmounts = collect();

//        TODO: you need to use bulk read maybe to remove where query from loop
        foreach ($tagIds as $tagId) {
            $transactions = Transaction::where('tag_id', $tagId)->get();
            $transactionAmounts = $transactionAmounts->merge($transactions);
        }

        return $transactionAmounts;
    }
}
