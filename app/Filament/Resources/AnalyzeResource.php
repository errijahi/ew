<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyzeResource\Pages;
use App\Models\Account;
use App\Models\Analyze;
use App\Models\Category;
use App\Models\Payee;
use App\Models\RecurringItem;
use App\Models\Tag;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\SelectAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $selectedModel = Tag::get();
        $Model = session('key');

        if ($Model === 'tag') {
            $selectedModel = Tag::get();
        } elseif ($Model === 'categories') {
            $selectedModel = Category::get();
        } elseif ($Model === 'account') {
            $selectedModel = Account::get();
        } elseif ($Model === 'recurring') {
            $selectedModel = RecurringItem::get();
        } elseif ($Model === 'payee') {
            $selectedModel = Payee::get();
        }

        $perPage = is_numeric(session('perPage')) ? (int) session('perPage') : 5;
        $selectedPeriod = is_string(session('status')) ? session('status') : 'year';
        $timeRange = is_string(session('timeRange')) ? session('timeRange') : 'last 7 days';
        $currentYear = Carbon::now()->year;
        $startDateRange = is_string(session('startDate')) ? (string) session('startDate') : null;
        $endDateRange = is_string(session('endDate')) ? (string) session('endDate') : null;
        $startYear = $currentYear - 5;
        $viewType = is_string(session('viewType')) ? session('viewType') : 'table';

        $transactionDataByPeriod = [];
        $transactionValues = Transaction::get()->all();
        $tableValues = '';

        if ($selectedPeriod === 'year') {
            if ($timeRange === 'last 3 years') {
                $startYear = Carbon::now()->year - 2;
            }

            if ($startDateRange) {
                $startYear = Carbon::createFromFormat('Y-m-d', $startDateRange)?->year;
            }

            if ($endDateRange) {
                $currentYear = Carbon::createFromFormat('Y-m-d', $endDateRange)?->year;
            }

            for ($year = $startYear; $year <= $currentYear; $year++) {
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel,
                    'year');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $transactionDataByPeriod[$year] = $tableValues;
            }
        } elseif ($selectedPeriod === 'month') {
            $startDate = Carbon::now()->subMonths(5)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            if ($timeRange === 'last 3 months') {
                $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
            }

            if ($startDateRange) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDateRange)?->startOfMonth();
            }

            if ($endDateRange) {
                $endDate = Carbon::createFromFormat('Y-m-d', $endDateRange)?->endOfMonth();
            }

            while (! is_null($startDate) && $startDate <= $endDate) {
                $monthName = $startDate->format('F');

                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel,
                    'month');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $transactionDataByPeriod[$monthName] = $tableValues;

                $startDate->addMonth();
            }
        } elseif ($selectedPeriod === 'week') {
            $numberOfWeeks = 6;
            if ($timeRange === 'last 4 weeks') {
                $numberOfWeeks = 3;
            }

            $startOfWeek = Carbon::now()->subWeeks($numberOfWeeks)->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            if ($startDateRange) {
                $startOfWeek = Carbon::createFromFormat('Y-m-d', $startDateRange)?->startOfWeek();
            }

            if ($endDateRange) {
                $endOfWeek = Carbon::createFromFormat('Y-m-d', $endDateRange)?->endOfWeek();
            }

            $weeks = [];
            while ($startOfWeek->lte($endOfWeek)) {
                $weeks[] = [
                    'start' => $startOfWeek->copy(),
                    'end' => $startOfWeek->copy()->endOfWeek(),
                ];
                $startOfWeek->addWeek();
            }

            foreach ($weeks as $week) {
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel,
                    'week');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $weekLabel = $week['start']->format('d M').' - '.$week['end']->format('d M');
                $transactionDataByPeriod[$weekLabel] = $tableValues;
            }

        } elseif ($selectedPeriod === 'day') {
            $numberOfDays = 7;

            if ($timeRange === 'last 30 days') {
                $numberOfDays = 30;
            }

            $startDate = Carbon::now()->subDays($numberOfDays)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            if ($startDateRange) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDateRange)?->startOfDay();
            }

            if ($endDateRange) {
                $endDate = Carbon::createFromFormat('Y-m-d', $endDateRange)?->endOfDay();
            }

            $days = [];
            while ($startDate->lte($endDate)) {
                $days[] = $startDate->copy();
                $startDate->addDay();
            }

            foreach ($days as $day) {
                $aggregatedTransactionValues = Transaction::aggregateTransactionValues($transactionValues, $selectedModel,
                    'day');
                $tableValues = $aggregatedTransactionValues['tableValues'];
                $dayLabel = $day->format('d M');
                $transactionDataByPeriod[$dayLabel] = $tableValues;
            }
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $selectedModelArray = $selectedModel->toArray();
        $currentItems = array_slice($selectedModelArray, ($currentPage - 1) * $perPage, $perPage);

        $paginatedData = new LengthAwarePaginator(
            $currentItems,
            count($selectedModelArray),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        $table->content(
            view('livewire.analyze-table-view', compact(
                'tableValues',
                'paginatedData',
                'transactionDataByPeriod',
                'viewType',
                'selectedPeriod',
            ))
        );

        if ($viewType === 'table') {
            $table->contentFooter(view('livewire.analyze-table-footer', compact(
                'paginatedData', 'perPage'
            )));
        }

        $columns[] = TextColumn::make('placeholder_column')->label('This needs to return something to work,
            it is here only for that reason.');

        return $table->columns($columns)->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyzes::route('/'),
        ];
    }
}
