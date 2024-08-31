<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Enums\Days;
use App\Enums\NumberComparisonType;
use App\Enums\Priority;
use App\Enums\TextMatchType;
use App\Filament\Resources\RulesResource\Pages;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringItem;
use App\Models\Rule;
use App\Models\Tag;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RulesResource extends Resource
{
    protected static ?string $model = Rule::class;

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
                                        ->options(Account::where('team_id', $teamId)->pluck('account_name', 'id')->toArray())
                                        ->reactive(),
                                ],
                                default => [],
                            }),
                    ])->reorderable(false)->maxItems(6),

                Repeater::make('then_actions')
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
                                    'do_not_create_rule' => 'do not create a rule',
                                    'split_transaction' => 'split transaction',
                                    'mark_as_reviewed' => 'mark as reviewed',
                                    'mark_as_unreviewed' => 'mark as unreviewed',
                                    'send_me_email' => 'send me an email',
                                ]
                            )
                            ->reactive(),
                        Grid::make(2)
                            ->schema(fn (Get $get): array => match ($get('then')) {
                                'set_payee' => [
                                    TextInput::make('set_payee'),
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
                                    TextInput::make('set_uncategorized')
                                        ->label('')
                                        ->placeholder('Set uncategorized')
                                        ->disabled(),
                                ],
                                'add_tags' => [
                                    Select::make('add_tags')
                                        ->options(Tag::where('team_id', $teamId)->pluck('name', 'id')->toArray())
                                        ->reactive(),
                                ],
                                'delete_transaction' => [
                                    TextInput::make('delete_transaction')
                                        ->label('')
                                        ->placeholder('Delete transaction')
                                        ->disabled(),
                                ],
                                'link_to_recurring_item' => [
                                    Select::make('link_to_recurring_item')
                                        ->options(RecurringItem::where('team_id', $teamId)->pluck('name', 'id')->toArray())
                                        ->reactive(),
                                ],
                                'do_not_link_to_recurring_item' => [
                                    TextInput::make('do_not_link_to_recurring_item')
                                        ->label('')
                                        ->placeholder('Do not link to recurring item')
                                        ->disabled(),
                                ],
                                'do_not_create_rule' => [
                                    TextInput::make('do_not_create_rule')
                                        ->label('')
                                        ->placeholder('Do not create a rule')
                                        ->disabled(),
                                ],
                                'split_transaction' => [
                                    Repeater::make('split_transaction_repeater')
                                        ->schema([
                                            Grid::make(1)
                                                ->schema(fn (Get $get): array => [
                                                    TextInput::make('amount_percentages')
                                                        ->label('amount')
                                                        ->placeholder('Enter value in percentages'),
                                                    Toggle::make('mark_as_reviewed')
                                                        ->reactive(),
                                                    Toggle::make('run_split_transaction_trough_rules'),

                                                    Grid::make(4)
                                                        ->schema(function (Get $get) use ($teamId): array {
                                                            return [
                                                                ToggleButtons::make('category_button')
                                                                    ->multiple()
                                                                    ->label('')
                                                                    ->options([
                                                                        'category' => 'Category',
                                                                    ])
                                                                    ->reactive(),

                                                                ToggleButtons::make('payee_button')
                                                                    ->multiple()
                                                                    ->label('')
                                                                    ->options([
                                                                        'payee' => 'Payee',
                                                                    ])
                                                                    ->reactive(),

                                                                ToggleButtons::make('note_button')
                                                                    ->multiple()
                                                                    ->label('')
                                                                    ->options([
                                                                        'notes' => 'Notes',
                                                                    ])
                                                                    ->reactive(),

                                                                ToggleButtons::make('tag_button')
                                                                    ->multiple()
                                                                    ->label('')
                                                                    ->options([
                                                                        'tags' => 'Tags',
                                                                    ])
                                                                    ->reactive(),

                                                                Grid::make(2)
                                                                    ->schema(function (Get $get) use ($teamId): array {
                                                                        return [

                                                                            ...($get('category_button') === ['category'] ? [
                                                                                Select::make('set_category_split_transaction')
                                                                                    ->options(Category::where('team_id', $teamId)
                                                                                        ->pluck('name', 'id')
                                                                                        ->toArray())
                                                                                    ->reactive(),
                                                                            ] : []),

                                                                            ...($get('payee_button') === ['payee'] ? [
                                                                                TextInput::make('set_payee_split_transaction')
                                                                                    ->label('payee'),
                                                                            ] : []),

                                                                            ...($get('note_button') === ['notes'] ? [
                                                                                TextInput::make('set_note_split_transaction')
                                                                                    ->label('notes'),
                                                                            ] : []),

                                                                            ...($get('tag_button') === ['tags'] ? [
                                                                                Select::make('set_tag_split_transaction')
                                                                                    ->options(Tag::where('team_id', $teamId)
                                                                                        ->pluck('name', 'id')
                                                                                        ->toArray())
                                                                                    ->reactive(),
                                                                            ] : []),
                                                                        ];
                                                                    }),
                                                            ];
                                                        }),
                                                ]),
                                        ])->reorderable(false)->columnSpan(2),
                                ],
                                'mark_as_reviewed' => [
                                    TextInput::make('mark_as_reviewed')
                                        ->label('')
                                        ->placeholder('Mark as reviewed')
                                        ->disabled(),
                                ],
                                'mark_as_unreviewed' => [
                                    TextInput::make('mark_as_unreviewed')
                                        ->label('')
                                        ->placeholder('Mark as unreviewed')
                                        ->disabled(),
                                ],
                                'send_me_email' => [
                                    TextInput::make('send_me_email')
                                        ->label('')
                                        ->placeholder('Send me an email')
                                        ->disabled(),
                                ],
                                default => [],
                            }),
                    ])->reorderable(false)->maxItems(13),
            ]);
    }

    //    TODO: Make something to show split_transaction like split1 20% and split%80
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priority'),
                TextColumn::make('rule_trigger')
                    ->getStateUsing(function ($record) {
                        $response = '';

                        foreach ($record->ifAction as $getIfAction) {
                            if ($getIfAction['payee_filter_id']) {
                                $response .= ' '.'payee name = '.' '.$getIfAction->paye?->payee_name.'<br>';
                            }

                            if ($getIfAction['category_id']) {
                                $response .= ' '.'matches category = '.' '.$getIfAction->category?->name.'<br>';
                            }

                            if ($getIfAction['note_id']) {
                                $response .= ' '.'matches notes = '.' '.$getIfAction->note?->note.'<br>';
                            }

                            if ($getIfAction['day_id']) {
                                $response .= ' '.'matches day = '.' '.$getIfAction->day?->day.'<br>';
                            }

                            if ($getIfAction['account_id']) {
                                $response .= ' '.'in account = '.' '.$getIfAction->account?->account_name.'<br>';
                            }

                            if ($getIfAction['amount_id']) {
                                $response .= ' '.'amount = '.' '.$getIfAction->amount?->amount.'<br>';
                            }
                        }

                        return $response;
                    })->html(),
                TextColumn::make('rule_effect')
                    ->getStateUsing(function ($record) {
                        $response = '';
                        foreach ($record->thenAction as $getThenAction) {
                            if ($getThenAction['set_payee']) {
                                $response .= ' '.'set payee = '.' '.$getThenAction['set_payee'].'<br>';
                            }

                            if ($getThenAction['set_notes']) {
                                $response .= ' '.'set notes = '.' '.$getThenAction['set_notes'].'<br>';
                            }

                            if ($getThenAction['category_id']) {
                                $response .= ' '.'set_category = '.' '.$getThenAction?->category->name.'<br>';
                            }

                            if ($getThenAction['set_uncategorized']) {
                                $response .= ' '.'set_uncategorized = '.' '.$getThenAction['set_uncategorized'].'<br>';
                            }

                            if ($getThenAction['tag_id']) {
                                $response .= ' '.'add_tag = '.' '.$getThenAction->tag?->name.'<br>';
                            }

                            if ($getThenAction['delete_transaction']) {
                                $response .= ' '.'delete_transaction = '.' '.$getThenAction['delete_transaction'].'<br>';
                            }

                            if ($getThenAction['recurring_item_id']) {
                                $response .= ' '.'link_to_recurring_item = '.' '.$getThenAction->recurringItem?->name.'<br>';
                            }

                            if ($getThenAction['do_not_link_to_recurring_item']) {
                                $response .= ' '.'do_not_link_to_recurring_item = '.' '.$getThenAction['do_not_link_to_recurring_item'].'<br>';
                            }

                            if ($getThenAction['do_not_create_rule']) {
                                $response .= ' '.'do_not_create_rule = '.' '.$getThenAction['do_not_create_rule'].'<br>';
                            }

                            if ($getThenAction['rule_split_transaction_id']) {
                                $response .= ' '.'split_transaction = '.' '.$getThenAction->splitTransaction?->day.'<br>';
                            }

                            if ($getThenAction['mark_as_reviewed']) {
                                $response .= ' '.'mark_as_reviewed = '.' '.$getThenAction['mark_as_reviewed'].'<br>';
                            }

                            if ($getThenAction['mark_as_unreviewed']) {
                                $response .= ' '.'mark_as_unreviewed = '.' '.$getThenAction['mark_as_unreviewed'].'<br>';
                            }

                            if ($getThenAction['send_me_email']) {
                                $response .= ' '.'send_me_email = '.' '.$getThenAction['send_me_email'].'<br>';
                            }
                        }

                        return $response;
                    })->html(),
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
