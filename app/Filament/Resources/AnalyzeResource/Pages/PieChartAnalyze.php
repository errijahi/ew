<?php

namespace App\Filament\Resources\AnalyzeResource\Pages;

use App\Filament\Resources\AnalyzeResource;
use App\Filament\Resources\AnalyzeResource\Widgets\PieChartAnalyze as PieChartWidget;
use Filament\Resources\Pages\Page;

class PieChartAnalyze extends Page
{
    protected static string $resource = AnalyzeResource::class;

    protected static string $view = 'filament.resources.analyze-resource.pages.pie-chart-analyze';

    protected function getHeaderWidgets(): array
    {
        return [
            PieChartWidget::class,
        ];
    }
}
