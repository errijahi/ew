<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Budget;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()->teams[0]->id;
        $data['team_id'] = $teamId;
        $record = $this->getModel()::create($data);

        $budgetData['category_id'] = $record['id'];
        $budgetData['team_id'] = $record['team_id'];
        Budget::create($budgetData);

        return $record;
    }
}
