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

    public function mount(): void
    {
        $team = $this->createTeam();
        $this->redirectToTeamAccounts($team->id);
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    protected function handleRegistration(array $data): Team|TeamUser
    {
        return $this->createTeam();
    }

    protected function createTeam(): Team
    {
        $user = auth()->user();

        $team = Team::create([
            'user_id' => $user?->id,
            'name' => $user?->name.'\'s Team',
            'personal_team' => 1,
        ]);

        $team->members()->attach($user?->id);

        return $team;
    }

    protected function redirectToTeamAccounts(int $teamId)
    {
        return redirect()->to("/admin/{$teamId}/accounts");
    }
}
