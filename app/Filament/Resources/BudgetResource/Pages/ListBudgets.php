<?php

namespace App\Filament\Resources\BudgetResource\Pages;

use App\Filament\Resources\BudgetResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $filters = $this->tableFilters;
        $year = $filters['year']['value'] ?? null;

        $query->leftJoin('budgets', function ($join) use ($year) {
            $join->on('categories.id', '=', 'budgets.category_id')
                ->where('budgets.year', $year);
        })->select('categories.*', 'budgets.budget', 'budgets.year');

        return $query;
    }
}
