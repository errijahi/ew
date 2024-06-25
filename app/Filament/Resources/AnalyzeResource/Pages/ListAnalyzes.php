<?php

namespace App\Filament\Resources\AnalyzeResource\Pages;

use AllowDynamicProperties;
use App\Filament\Resources\AnalyzeResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringItem;
use App\Models\Tag;
use App\Models\Transaction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\On;

class ListAnalyzes extends ListRecords
{
    protected static string $resource = AnalyzeResource::class;

    public string $status;

    public string $timeRange;
    public mixed $tabValue;

    public ?string $currentTab = 'tags';

    public function getTabs(): array
    {
        $this->status = session('status', 'year');
        $this->timeRange = session('timeRange', 'last 6 years');
        $teamId = auth()->user()->teams[0]->id;
        $getValues = ['tags', 'categories', 'accounts', 'recurring', 'payee'];

        foreach ($getValues as $name) {

            $slug = str($name)->slug()->toString();

            $tabs[$slug] = Tab::make($name)
                ->modifyQueryUsing(function ($query) use ($name, $teamId) {
                    $key = '';

                    switch ($name) {
                        case 'tags':
                            $query = Tag::where('team_id', $teamId);
                            $key = 'tag';
                            break;
                        case 'categories':
                            $query = Category::where('team_id', $teamId);
                            $key = 'categories';
                            break;
                        case 'accounts':
                            $query = Account::where('team_id', $teamId);
                            $key = 'account';
                            break;
                        case 'recurring':
                            $query = RecurringItem::where('team_id', $teamId);
                            $key = 'recurring';
                            break;
                        case 'payee':
                            $query = Transaction::where('team_id', $teamId);
                            $key = 'payee';
                            break;
                    }

                    Session::put('key', $key);
                    $this->dispatch('created');

                    return $query;
                });
        }

        return $tabs;
    }


    #[NoReturn]
    public function createTimeRange(): void
    {
        session(['timeRange' => $this->timeRange ?? 'last 7 days']);
        $this->dispatch('created');
    }

    #[NoReturn]
    public function create(): void
    {
        session(['status' => $this->status ?? 'year']);
        $this->dispatch('created');
    }

    #[NoReturn] #[On('created')]
    public function refresh(): void
    {
    }
}
