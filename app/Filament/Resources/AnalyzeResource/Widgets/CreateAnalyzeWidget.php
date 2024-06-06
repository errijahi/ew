<?php

namespace App\Filament\Resources\AnalyzeResource\Widgets;

use App\Models\Tag;
use Filament\Widgets\Widget;
use JetBrains\PhpStorm\NoReturn;

class CreateAnalyzeWidget extends Widget
{
    public string $status;

    public string $tableModel;

    public $getTable = 'thisMonth';

    protected int|string|array $columnSpan = [
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 2,    ];

    public function getColumnSpan(): int|string|array
    {
        //        dd($this->columnSpan);
        return $this->columnSpan;
    }

    public function mount(): void
    {
        $this->status = session('status', 'year');
        $this->tableModel = session('tableModel', Tag::class);
    }

    protected static string $view = 'filament.resources.analyze-resource.widgets.create-analyze-widget';

    #[NoReturn]
    public function create(): void
    {
        session(['status' => $this->status ?? 'year']);
        $this->dispatch('analyze-created');
    }

    #[NoReturn]
    public function getTable(): void
    {
        $this->dispatch('analyze-created');
    }
}
