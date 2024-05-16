<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RulesResource\Pages;
use App\Models\Rules;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class RulesResource extends Resource
{
    protected static ?string $model = Rules::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TextInput::make('priority')->required()->default(10),
                Toggle::make('stop_processing_other_rules'),
                Toggle::make('delete_this_rule_after_use'),
                Toggle::make('rule_on_transaction_update'),
                Select::make('if_actions')
                    ->options(
                        [
                            'Matches payee name',
                            'Matches category',
                            'Matches notes',
                            'Matches amount',
                            'Matches day',
                            'In account'
                        ])
                    ->native(false),
                    TextInput::make('priority')
                    ->label('Payee Name')
                    ->required()
                    ->when('if_actions', fn($value) => $value === 'Matches payee name'),
                    Select::make('then_actions')
                    ->options(
                        [
                            'Set payee',
                            'Matches category',
                            'Matches notes',
                            'Matches amount',
                            'Matches day',
                            'In account'
                        ])
                    ->native(false),
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
            'index' => Pages\ListRules::route('/'),
            'create' => Pages\CreateRules::route('/create'),
            'edit' => Pages\EditRules::route('/{record}/edit'),
        ];
    }
}
