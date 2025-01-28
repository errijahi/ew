<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget',
        'category_id',
        'team_id',
        'year',
        'month',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function calculateBudgetPeriods(
        $record,
        $adjustYear = 0,
        $totalTransaction = false,
        $lastPeriodDifference = false
    ): string {
        $year = session('selected_year');
        $month = session('selected_month') - $adjustYear;

        if (($adjustYear !== 0) && $month < 1) {
            $year--;
            $month = 12;
        }

        if ($adjustYear === 0) {
            $results = self::calculateBudgetWhereYearMonth($record, $year, $month) - $record->transactions_sum_amount;
        } elseif ($lastPeriodDifference) {
            $results = self::calculateBudgetWhereYearMonth($record, $year, $month)
                - self::calculateBudgetWhereInBetweenYearMonth($record, $year, $month);
        } else {
            $results = $totalTransaction ? self::calculateBudgetWhereInBetweenYearMonth($record, $year, $month)
                : self::calculateBudgetWhereYearMonth($record, $year, $month);
        }

        return number_format(($results), 2);
    }

    private static function calculateBudgetWhereYearMonth($record, $year, $month)
    {
        return self::where('category_id', $record->id)
            ->where('team_id', $record->team_id)
            ->where('year', $year)
            ->where('month', $month)
            ->value('budget');
    }

    private static function calculateBudgetWhereInBetweenYearMonth($record, $year, $month)
    {
        return Transaction::where('category_id', $record->id)
            ->where('team_id', $record->team_id)
            ->whereBetween('created_at', [
                Carbon::create($year, $month, 1)?->startOfDay(),
                Carbon::create($year, $month, 1)?->endOfMonth()->endOfDay(),
            ])
            ->sum('amount');
    }
}
