<?php

namespace App\Filament\Resources;

use App\Enums\Cadence;
use App\Filament\Resources\RecurringItemResource\Pages;
use App\Models\RecurringItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecurringItemResource extends Resource
{
    protected static ?string $model = RecurringItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('description'),
                DateTimePicker::make('billing_date')->required(),
                DateTimePicker::make('start_date')->required(),
                DateTimePicker::make('end_date')->required(),
                Select::make('repeating_cadence')
                    ->options(Cadence::values())
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('billing_date'),
                TextColumn::make('description')->words(3),
                TextColumn::make('start_date'),
                TextColumn::make('end_date'),
                TextColumn::make('repeating_cadence'),
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
            'index' => Pages\ListRecurringItems::route('/'),
            'create' => Pages\CreateRecurringItem::route('/create'),
            'edit' => Pages\EditRecurringItem::route('/{record}/edit'),
        ];
    }
}
