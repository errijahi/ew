<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\BudgetFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    /** @use HasFactory<BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'budget',
        'category_id',
        'team_id',
        'year',
        'month',
    ];

    /**
     * @return BelongsTo<Category,$this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @param Collection<int, Budget> $record
     * @param int $adjustYear
     * @param bool $totalTransaction
     * @param bool $lastPeriodDifference
     * @return string
     */
    public static function calculateBudgetPeriods(
        Collection $record,
        int $adjustYear = 0,
        bool $totalTransaction = false,
        bool $lastPeriodDifference = false
    ): string {
        // I am not using null safe operators after session() cause it will never be null
        // This complicated code for year and month is needed to make sure that types are int
        $year = session()->has('selected_year') && is_numeric(session('selected_year'))
            ? (int) session('selected_year')
            : 0;

        $month = session()->has('selected_month') && is_numeric(session('selected_month'))
            ? (int) session('selected_month')
            : 1;

        $month -= $adjustYear;

        if (($adjustYear !== 0) && $month < 1) {
            $year--;
            $month = 12;
        }

        if ($adjustYear === 0) {
            $results = self::calculateBudgetWhereYearMonth($record, $year, $month) - $record->transactions_sum_amount;
        } elseif ($lastPeriodDifference) {
            $results = self::calculateBudgetWhereYearMonth($record, $year, $month)
                - (float) self::calculateBudgetWhereInBetweenYearMonth($record, $year, $month);
        } else {
            $results = $totalTransaction ? self::calculateBudgetWhereInBetweenYearMonth($record, $year, $month)
                : self::calculateBudgetWhereYearMonth($record, $year, $month);
        }

        return number_format(((float) $results), 2);
    }

    /**
     * @param Collection<int, Budget> $record
     * @param int $year
     * @param float|int $month
     * @return float|int
     */
    private static function calculateBudgetWhereYearMonth(
        Collection $record,
        int $year,
        float|int $month
    ): float|int
    {
        return self::where('category_id', $record->id)
            ->where('team_id', $record->team_id)
            ->where('year', $year)
            ->where('month', $month)
            ->value('budget');
    }

    /**
     * @param Collection<int, Budget> $record
     * @param int $year
     * @param int|null $month
     * @return float|int|string
     */
    private static function calculateBudgetWhereInBetweenYearMonth(
        Collection $record,
        int $year,
        int|null $month
    ): float|int|string
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
