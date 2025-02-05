<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SuperAdminPanelResource\Pages;
use App\Models\SuperAdminPanel;
use App\Models\TeamUser;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuperAdminPanelResource extends Resource
{
    protected static ?string $model = SuperAdminPanel::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'SuperAdmin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuperAdminPanels::route('/'),
            'create' => Pages\CreateSuperAdminPanel::route('/create'),
            'edit' => Pages\EditSuperAdminPanel::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $getRole = TeamUser::where('user_id', auth()->user()?->id)->first();

        return $getRole?->role === 'super_admin';
    }
}
