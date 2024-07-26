<?php

namespace App\Filament\Resources\AnalyzeResource\Pages;

use App\Filament\Resources\AnalyzeResource;
use Filament\Resources\Pages\Page;

class PieChartAnalyze extends Page
{
    protected static string $resource = AnalyzeResource::class;

    protected static string $view = 'filament.resources.analyze-resource.pages.pie-chart-analyze';
}
