<?php

namespace App\Filament\Resources\AnalyzeResource\Widgets;

use Filament\Widgets\Widget;
use JetBrains\PhpStorm\NoReturn;

class CreateAnalyzeWidget extends Widget
{
    public string $status;

    public function mount(): void
    {
        $this->status = session('status', 'year');
    }

    protected static string $view = 'filament.resources.analyze-resource.widgets.create-analyze-widget';

    #[NoReturn]
    public function create(): void
    {
        session(['status' => $this->status ?? 'year']);
        $this->dispatch('analyze-created');
    }
}
