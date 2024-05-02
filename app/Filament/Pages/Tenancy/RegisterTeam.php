<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use App\Models\TeamUser;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Proceed to your new account';
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    protected function handleRegistration(array $data): Team|TeamUser
    {
        $team = Team::create([
            'user_id' => auth()->user()->id,
            'name' => auth()->user()->name . '\'s Team',
            'personal_team' => 1,
        ]);

        $team->members()->attach(auth()->user()->id);

        return $team;
    }
}
