<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Enums\Days;
use App\Enums\NumberComparisonType;
use App\Enums\Priority;
use App\Enums\TextMatchType;
use App\Filament\Resources\RulesResource\Pages;
use App\Models\Category;
use App\Models\Rules;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RulesResource extends Resource
{
    protected static ?string $model = Rules::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Setup';

    public static function form(Form $form): Form
    {
        $teamId = auth()->user()->teams[0]->id;

        return $form
            ->schema([
                Select::make('priority')
                    ->options(Priority::values())
                    ->default(Priority::P10)
                    ->native(false),
                Toggle::make('stop_processing_other_rules'),
                Toggle::make('delete_this_rule_after_use'),
                Toggle::make('rule_on_transaction_update'),

                Repeater::make('if_actions')
                    ->schema([
                        Select::make('if')
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->options(
                                [
                                    'matches_payee_name' => 'Matches payee name',
                                    'matches_category' => 'Matches category',
                                    'matches_notes' => 'Matches notes',
                                    'matches_amount' => 'Matches amount',
                                    'matches_day' => 'Matches day',
                                    'in_account' => 'In account',
                                ]
                            )
                            ->reactive(),
                        Grid::make(2)
                            ->schema(fn (Get $get): array => match ($get('if')) {
                                'matches_payee_name' => [
                                    TextInput::make('payee_name'),
                                    Select::make('filter')
                                        ->options(TextMatchType::values())
                                        ->reactive(),
                                ],
                                'matches_category' => [
                                    Select::make('category')
                                        ->options(Category::where('team_id', $teamId)->pluck('name', 'id')->toArray())
                                        ->reactive(),
                                ],
                                'matches_notes' => [
                                    TextInput::make('note'),
                                    Select::make('filter')
                                        ->options(TextMatchType::values())
                                        ->reactive(),
                                ],
                                'matches_amount' => [
                                    TextInput::make('amount'),
                                    Select::make('type')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                    Select::make('filter')
                                        ->options(NumberComparisonType::values())
                                        ->reactive(),
                                ],
                                'matches_day' => [
                                    Select::make('day')
                                        ->options(Days::values())
                                        ->reactive(),
                                    Select::make('filter')
                                        ->options(NumberComparisonType::values())
                                        ->reactive(),
                                ],
                                'in_account' => [
                                    Select::make('type')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                default => [],
                            }),
                    ])->reorderable(false),

                Repeater::make('Then')
                    ->schema([
                        Select::make('then')
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->options(
                                [
                                    'set_payee' => 'set payee',
                                    'set_notes' => 'set notes',
                                    'set_category' => 'set category',
                                    'set_uncategorized' => 'set uncategorized',
                                    'add_tags' => 'add tags',
                                    'delete_transaction' => 'delete transaction',
                                    'link_to_recurring_item' => 'link to recurring item',
                                    'do_not_link_to_recurring_item' => 'do not link to recurring item',
                                    'do_not_create_a_rule' => 'do not create a rule',
                                    'split_transaction' => 'split transaction',
                                    'mark_as_reviewed' => 'mark as reviewed',
                                    'mark_as_unreviewed' => 'mark as unreviewed',
                                    'send_me_an_email' => 'send me an email',
                                ]
                            )
                            ->reactive(),
                        Grid::make(2)
                            ->schema(fn (Get $get): array => match ($get('then')) {
                                'set_payee' => [
                                    TextInput::make('set_payee')->label('')->placeholder('Enter the payee name')->disabled(),
                                ],
                                'set_notes' => [
                                    TextInput::make('set_notes'),
                                ],
                                'set_category' => [
                                    Select::make('set_category')
                                        ->options(Category::where('team_id', $teamId)->pluck('name', 'id')->toArray())
                                        ->reactive(),
                                ],
                                'set_uncategorized' => [
                                    TextInput::make('name')
                                        ->default('John'),
                                ],
                                'add_tags' => [
                                    Select::make('add_tags')
                                        ->options(Days::values())
                                        ->reactive(),
                                    Select::make('filter')
                                        ->options(NumberComparisonType::values())
                                        ->reactive(),
                                ],
                                'delete_transaction' => [
                                    Select::make('delete_transaction')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'link_to_recurring_item' => [
                                    Select::make('link_to_recurring_item')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'do_not_link_to_recurring_item' => [
                                    Select::make('do_not_link_to_recurring_item')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'do_not_create_a_rule' => [
                                    Select::make('do_not_create_a_rule')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'split_transaction' => [
                                    Select::make('split_transaction')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'mark_as_reviewed' => [
                                    Select::make('mark_as_reviewed')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'mark_as_unreviewed' => [
                                    Select::make('mark_as_unreviewed')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                'send_me_an_email' => [
                                    Select::make('send_me_an_email')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],'' => [
                                    Select::make('')
                                        ->options(AccountType::values())
                                        ->reactive(),
                                ],
                                default => [],
                            }),
                    ])->reorderable(false),
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
