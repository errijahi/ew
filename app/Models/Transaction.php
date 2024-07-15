<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'payee_id',
        'notes',
        'amount',
        'status',
        'transaction_source',
        'team_id',
    ];

    protected $with = ['payee'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recurringItem()
    {
        return $this->belongsTo(RecurringItem::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public static function calculateTransaction(
        $selectedModel,
        $searchBy,
        $period,
        $year = null,
        $currentDate = null,
        $week = null,
        $day = null): array
    {
        $totalTransactionsSum = 0;
        $totalTagsCount = 0;

        foreach ($selectedModel as $model) {
            $modelId = $model->id;
            $query = self::where($searchBy, $modelId);

            switch ($period) {
                case 'year':
                    $query->whereYear('created_at', $year);
                    break;
                case 'month':
                    $query->whereYear('created_at', $year)
                        ->whereMonth('created_at', $currentDate->month);
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$week['start'], $week['end']]);
                    break;
                case 'day':
                    $query->whereDate('created_at', $day);
                    break;
            }

            $transactionsSum = $query->sum('amount');

            if ($transactionsSum > 0) {
                $totalTransactionsSum += $transactionsSum;
                $totalTagsCount++;
            }
        }

        if ($totalTagsCount > 0) {
            $averageAmount = $totalTransactionsSum / $totalTagsCount;
        } else {
            $averageAmount = 0;
        }

        return [
            'transactionsSum' => $totalTransactionsSum,
            'averageAmount' => $averageAmount,
            'totalTransactionsSum' => $totalTransactionsSum,
            'totalTagsCount' => $totalTagsCount
        ];
    }
}
