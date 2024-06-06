<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Analyze;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

class AnalyzeResource extends Resource
{
    protected static ?string $model = Analyze::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function getWidgets(): array
    {
        return [
            AnalyzeResource\Widgets\CreateAnalyzeWidget::class,
        ];
    }

    public static function table(Table $table): Table
    {
        $columns = [
            TextColumn::make('name')
                ->getStateUsing(function ($record) {
                    return $record->name ?? ($record->account_name ?? $record->payee);
                }),
        ];

        $selectedPeriod = session('status') ?? 'year';
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5; // Start from 6 years ago, It shows last 6 years, should be dynamic.
        $lastMonth = Carbon::now()->subMonth();
        $daysInLastMonth = $lastMonth->daysInMonth;

        if ($selectedPeriod === 'month') {
            for ($month = 1; $month <= 12; $month++) {
                //                $transactionMonthCount = Transaction::whereMonth('created_at', $month)->count();
                $monthName = Carbon::create()?->month($month)->format('F');

                $columns[] = TextColumn::make('month_'.$month)
//                    ->numeric()
                    ->summarize(
                        Summarizer::make()
                            ->using(function (Builder $query) use ($month): int {
                                return Transaction::whereMonth('created_at', $month)
                                    ->sum('amount');
                            })
                    )
                    ->summarize(
                        Summarizer::make()
                            ->using(function (Builder $query) use ($month): int {
                                //                                dd(Transaction::whereMonth('created_at', $month)->count());
                                //                                dd(Transaction::whereMonth('created_at', $month)
                                //                                    ->avg('amount',1));
                                return Transaction::whereMonth('created_at', $month)
                                    ->avg('id', 1);
                            })
                    )
                    ->label($monthName)
                    ->getStateUsing(function ($record) use ($month) {
                        $values = $record->getMonthlyData(null, null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $monthKey = $value->created_at->month;

                            if (isset($transactionData[$tagId][$monthKey])) {
                                $transactionData[$tagId][$monthKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$monthKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$month] ?? null;
                    });
            }
        } elseif ($selectedPeriod === 'year') {
            for ($year = $startYear; $year <= $currentYear; $year++) {
                $columns[] = TextColumn::make('year_'.$year)
                    ->label((string) $year)
                    ->getStateUsing(function ($record) use ($year) {
                        $values = $record->getMonthlyData(null, $year);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $yearKey = $value->created_at->year;

                            if (isset($transactionData[$tagId][$yearKey])) {
                                $transactionData[$tagId][$yearKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$yearKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$year] ?? null;
                    });
            }
        } elseif ($selectedPeriod === 'week') {
            $firstDayOfMonth = $lastMonth->copy()->startOfMonth();
            $lastDayOfMonth = $lastMonth->copy()->endOfMonth();

            $weeks = [];
            $startOfWeek = $firstDayOfMonth->copy()->startOfWeek();
            while ($startOfWeek->lte($lastDayOfMonth)) {
                $endOfWeek = $startOfWeek->copy()->endOfWeek();
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' => $endOfWeek,
                ];
                $startOfWeek->addWeek();
            }

            foreach ($weeks as $index => $week) {
                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');

                $columns[] = TextColumn::make('week_'.$index)
                    ->label($weekLabel)
                    ->getStateUsing(function ($record) use ($week) {
                        $values = $record->getMonthlyData($week['start'], $week['end']);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $weekKey = $value->created_at->weekOfYear;

                            if (isset($transactionData[$tagId][$weekKey])) {
                                $transactionData[$tagId][$weekKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$weekKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$week['start']->weekOfYear] ?? null;
                    });
            }
        } elseif ($selectedPeriod === 'day') {
            for ($day = 1; $day <= $daysInLastMonth; $day++) {
                $dayLabel = $lastMonth->copy()->day($day)->format('d M');

                $columns[] = TextColumn::make('day_'.$day)
                    ->label($dayLabel)
                    ->getStateUsing(function ($record) use ($lastMonth, $day) {
                        $dayDate = $lastMonth->copy()->day($day);
                        $values = $record->getMonthlyData($dayDate, null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $monthKey = $value->created_at->month;
                            $dayKey = $value->created_at->day;

                            if (isset($transactionData[$tagId][$monthKey][$dayKey])) {
                                $transactionData[$tagId][$monthKey][$dayKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$monthKey][$dayKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$lastMonth->month][$day] ?? null;
                    });
            }
        }

        return $table->columns($columns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyzes::route('/'),
        ];
    }
}
