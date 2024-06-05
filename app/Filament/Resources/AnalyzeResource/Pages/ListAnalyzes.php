<?php

namespace App\Filament\Resources\AnalyzeResource\Pages;

use App\Filament\Resources\AnalyzeResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringItem;
use App\Models\Tag;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\On;

class ListAnalyzes extends ListRecords
{
    protected static string $resource = AnalyzeResource::class;

    #[NoReturn] #[On('analyze-created')]
    public function refresh(): void
    {
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyzeResource\Widgets\CreateAnalyzeWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $teamId = auth()->user()->teams[0]->id;
        $getValues = ['tags', 'categories', 'accounts', 'recurring', 'payee'];

        foreach ($getValues as $name) {

            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make($name)
                ->modifyQueryUsing(function ($query) use ($name, $teamId) {

                    if ($name === 'tags') {
                        $test = Tag::where('team_id', $teamId);
                    }

                    if ($name === 'categories') {
                        $test = Category::where('team_id', $teamId);
                    }

                    if ($name === 'accounts') {
                        $test = Account::where('team_id', $teamId);
                    }

                    if ($name === 'recurring') {
                        $test = RecurringItem::where('team_id', $teamId);
                    }

                    if ($name === 'payee') {
                        $test = Transaction::where('team_id', $teamId);
                    }

                    return $test;
                });
        }

        return $tabs;
    }
}
