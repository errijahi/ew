<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Analyze;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\SelectAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

class AnalyzeResource extends Resource
{
    protected static ?string $model = Analyze::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function table(Table $table): Table
    {
        $table->headerActions([
            SelectAction::make('make custom table header filters')
                ->view('livewire.table-filter'),
        ]);

        $columns = [
            TextColumn::make('name')
                ->getStateUsing(function ($record) {
                    return $record->name ?? ($record->account_name ?? $record->payee);
                }),
        ];

        $selectedPeriod = session('status') ?? 'year';
        $timeRange = session('timeRange') ?? 'last 7 days';

        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 5;

        if ($selectedPeriod === 'month') {
            $startMonth = Carbon::now()->month - 5;
            $endMonth = Carbon::now()->month;

            if ($timeRange === 'last 3 months') {
                $startMonth = Carbon::now()->month - 2;
                $endMonth = Carbon::now()->month;
            }

            for ($month = $startMonth; $month <= $endMonth; $month++) {

                $monthName = Carbon::create()?->month($month)->format('F');
                $columns[] = TextColumn::make('month_'.$month)
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
                                $sum = Transaction::whereMonth('created_at', $month)->sum('amount');
                                $count = Transaction::whereMonth('created_at', $month)->count();

                                //TODO: there is a bag if one row is empty,second is full, and third is empty it returns avg divided by 2;
                                return ($count - 1) > 0 ? ($sum / ($count - 1) === $sum ? $sum / $count : $sum / ($count - 1)) : $sum / 1;
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

            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

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
            $numberOfWeeks = 3;

            if ($timeRange === 'last 6 weeks') {
                $numberOfWeeks = 6;
            }

            $startOfWeek = Carbon::now()->subWeeks($numberOfWeeks)->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $weeks = [];

            while ($startOfWeek->lte($endOfWeek)) {
                $weekEnd = $startOfWeek->copy()->endOfWeek();
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' => $weekEnd,
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
            $numberOfDays = 6;

            if ($timeRange === 'last 30 days') {
                $numberOfDays = 30;
            }

            $startDate = Carbon::now()->subDays($numberOfDays)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            for ($day = $startDate; $day <= $endDate; $day->addDay()) {
                $dayLabel = $day->format('d M');

                $columns[] = TextColumn::make('day_'.$day->day)
                    ->label($dayLabel)
                    ->getStateUsing(function ($record) use ($day) {
                        $values = $record->getMonthlyData($day, null);

                        $transactionData = [];
                        foreach ($values as $value) {
                            $tagId = $value->tag_id;
                            $dayKey = $value->created_at->day;

                            if (isset($transactionData[$tagId][$dayKey])) {
                                $transactionData[$tagId][$dayKey]['amount'] += $value->amount;
                            } else {
                                $transactionData[$tagId][$dayKey] = [
                                    'amount' => $value->amount,
                                ];
                            }
                        }

                        return $transactionData[$record->id][$day->day] ?? null;
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
