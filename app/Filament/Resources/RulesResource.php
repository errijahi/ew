<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Enums\Days;
use App\Enums\NumberComparisonType;
use App\Enums\Priority;
use App\Enums\TextMatchType;
use App\Filament\Resources\RulesResource\Pages;
use App\Models\Category;
use App\Models\Rule;
use App\Models\SplitTransaction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
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

                Repeater::make('ifAction')
                    ->label('Trigger')
                    ->relationship('ifAction')
                    ->schema([
                        Select::make('condition_type')
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
                            )->reactive()
                            ->afterStateHydrated(function ($state, callable $set, callable $get): void {
                                // If condition_type is not set, check if category_id exists and set the default value accordingly
                                if (! $state && $get('payee_filter_id')) {
                                    $set('condition_type', 'matches_payee_name');
                                }
                                if (! $state && $get('category_id')) {
                                    $set('condition_type', 'matches_category');
                                }
                                if (! $state && $get('note_id')) {
                                    $set('condition_type', 'matches_notes');
                                }
                                if (! $state && $get('amount_id')) {
                                    $set('condition_type', 'matches_amount');
                                }
                                if (! $state && $get('day_id')) {
                                    $set('condition_type', 'matches_day');
                                }
                                if (! $state && $get('account_id')) {
                                    $set('condition_type', 'in_account');
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set): void {
                                // Clear related fields when condition type changes
                                $set('matches_payee_name', null);
                                $set('matches_category', null);
                                $set('matches_notes', null);
                                $set('matches_amount', null);
                                $set('matches_day', null);
                                $set('in_account', null);
                            }),
                        Grid::make(2)
                            ->schema(function (callable $get) {
                                return match ($get('condition_type')) {
                                    'matches_payee_name' => [
                                        Grid::make(2)
                                            ->relationship('payee')
                                            ->schema([
                                                Select::make('payee_id')
                                                    ->label('Payee')
                                                    ->relationship('payeeName', 'name')
                                                    // searchable() for some reason this doesn't work
//                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm([
                                                        TextInput::make('name')
                                                            ->required()
                                                            ->label('Payee Name'),
                                                    ]),
                                                Select::make('filter')
                                                    ->options(TextMatchType::values())
                                                    ->reactive(),
                                            ]),
                                    ],
                                    'matches_category' => [
                                        Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->reactive(),
                                    ],
                                    'matches_notes' => [
                                        Grid::make(2)->relationship('note')
                                            ->schema([
                                                TextInput::make('note'),
                                                Select::make('filter')
                                                    ->options(TextMatchType::values())
                                                    ->reactive(),
                                            ]),
                                    ],
                                    'matches_amount' => [
                                        Grid::make(2)->relationship('amount')
                                            ->schema([
                                                TextInput::make('amount')
                                                    ->label('Amount')
                                                    ->numeric()
                                                    ->columnSpan(1),
                                                Select::make('type')
                                                    ->options(AccountType::values())
                                                    ->reactive()
                                                    ->label('Type')
                                                    ->columnSpan(1),
                                                Select::make('filter')
                                                    ->options(NumberComparisonType::values())
                                                    ->reactive()
                                                    ->label('Filter')
                                                    ->columnSpan(2),
                                            ]),
                                    ],
                                    'matches_day' => [
                                        Grid::make(2)->relationship('day')
                                            ->schema([
                                                Select::make('day')
                                                    ->options(Days::values())
                                                    ->reactive(),
                                                Select::make('filter')
                                                    ->options(NumberComparisonType::values())
                                                    ->reactive(),
                                            ]),
                                    ],
                                    'in_account' => [
                                        Select::make('account_id')
                                            ->label('Account')
                                            ->relationship('account', 'name')
                                            ->reactive(),
                                    ],
                                    default => [],
                                };
                            }),
                    ])
                    ->reorderable(false)
                    ->maxItems(6),

                Repeater::make('thenActions')
                    ->relationship('thenAction')
                    ->label('Effect')
                    ->schema([
                        Select::make('condition_type')
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->options([
                                'set_payee' => 'Set Payee',
                                'set_notes' => 'Set Notes',
                                'set_category' => 'Set Category',
                                'set_uncategorized' => 'Set Uncategorized',
                                'add_tags' => 'Add Tags',
                                'delete_transaction' => 'Delete Transaction',
                                'link_to_recurring_item' => 'Link to Recurring Item',
                                'do_not_link_to_recurring_item' => 'Do Not Link to Recurring Item',
                                'do_not_create_rule' => 'Do Not Create a Rule',
                                'split_transaction' => 'Split Transaction',
                                'reviewed' => 'Reviewed',
                                'send_me_email' => 'Send Me an Email',
                            ])
                            ->reactive()
                            ->afterStateHydrated(function ($state, callable $set, callable $get, $record): void {
                                //                                dd($record->splitTransaction);
                                // If condition_type is not set, check if category_id exists and set the default value accordingly
                                if (! $state && $get('tag_id')) {
                                    $set('condition_type', 'add_tags');
                                }
                                if (! $state && $get('category_id')) {
                                    $set('condition_type', 'set_category');
                                }
                                if (! $state && $get('recurring_item_id')) {
                                    $set('condition_type', 'link_to_recurring_item');
                                }
                                if (! $state && $record?->splitTransaction->isNotEmpty()) {
                                    $set('condition_type', 'split_transaction');
                                }
                                if (! $state && $get('payee_id')) {
                                    $set('condition_type', 'set_payee');
                                }
                                if (! $state && $get('set_notes')) {
                                    $set('condition_type', 'set_notes');
                                }
                                if (! $state && $get('set_uncategorized')) {
                                    $set('condition_type', 'set_uncategorized');
                                }
                                if (! $state && $get('delete_transaction')) {
                                    $set('condition_type', 'delete_transaction');
                                }
                                if (! $state && $get('do_not_link_to_recurring_item')) {
                                    $set('condition_type', 'do_not_link_to_recurring_item');
                                }
                                if (! $state && $get('do_not_create_rule')) {
                                    $set('condition_type', 'do_not_create_rule');
                                }
                                if (! $state && $get('reviewed')) {
                                    $set('condition_type', 'reviewed');
                                }
                                if (! $state && $get('send_me_email')) {
                                    $set('condition_type', 'send_me_email');
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set): void {
                                // Clear related fields when condition type changes
                                $set('set_payee', null);
                                $set('set_notes', null);
                                $set('set_category', null);
                                $set('set_uncategorized', null);
                                $set('add_tags', null);
                                $set('delete_transaction', null);
                                $set('link_to_recurring_item', null);
                                $set('do_not_link_to_recurring_item', null);
                                $set('do_not_create_rule', null);
                                $set('split_transaction', null);
                                $set('reviewed', null);
                                $set('send_me_email', null);
                            }),
                        Grid::make(2)
                            ->schema(function (callable $get) {
                                return match ($get('condition_type')) {
                                    'set_payee' => [
                                        Select::make('payee_id')
                                            ->relationship('payee', 'name')
                                            ->preload(),
                                    ],
                                    'set_notes' => [
                                        TextInput::make('set_notes')
                                            ->label('Notes')
                                            ->placeholder('Enter notes'),
                                    ],
                                    'set_category' => [
                                        Select::make('set_category')
                                            ->relationship('category', 'name')
                                            ->label('Category')
                                            ->reactive(),
                                    ],
                                    'set_uncategorized' => [
                                        ToggleButtons::make('set_uncategorized')
                                            ->label('Uncategorized')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    'add_tags' => [
                                        Select::make('add_tags')
                                            ->relationship('tag', 'name')
                                            ->label('Tags')
                                            ->reactive(),
                                    ],
                                    'delete_transaction' => [
                                        ToggleButtons::make('delete_transaction')
                                            ->label('Delete transaction')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    'link_to_recurring_item' => [
                                        Select::make('link_to_recurring_item')
                                            ->relationship('recurringItem', 'name')
                                            ->label('Recurring Item')
                                            ->reactive(),
                                    ],
                                    'do_not_link_to_recurring_item' => [
                                        ToggleButtons::make('do_not_link_to_recurring_item')
                                            ->label('Do not link to recurring item')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    'do_not_create_rule' => [
                                        ToggleButtons::make('do_not_create_rule')
                                            ->label('Do not create a rule')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    'split_transaction' => [
                                        Repeater::make('split_transaction_repeater')
                                            ->relationship('splitTransaction')
                                            ->columnSpan(2)
                                            ->schema([
                                                TextInput::make('amount')
                                                    ->label('Amount (%)')
                                                    ->placeholder('Enter percentage'),
                                                Toggle::make('reviewed')->label('Mark as Reviewed')->reactive(),
                                                Toggle::make('run_through_rules')->label('Run Through Rules')->reactive(),
                                                Grid::make(4)->schema(function (callable $get): array {
                                                    return [
                                                        ToggleButtons::make('category_button')
                                                            ->multiple()
                                                            ->label('Category')
                                                            ->options(['category' => 'Category'])
                                                            ->reactive(),
                                                        ToggleButtons::make('payee_button')
                                                            ->multiple()
                                                            ->label('Payee')
                                                            ->options(['payee' => 'Payee'])
                                                            ->reactive(),
                                                        ToggleButtons::make('note_button')
                                                            ->multiple()
                                                            ->label('Notes')
                                                            ->options(['notes' => 'Notes'])
                                                            ->reactive(),
                                                        ToggleButtons::make('tag_button')
                                                            ->multiple()
                                                            ->label('Tags')
                                                            ->options(['tags' => 'Tags'])
                                                            ->reactive(),
                                                        Grid::make(2)->schema(function (callable $get, $record): array {
                                                            return [
                                                                ...($get('category_button') === ['category']) ? [
                                                                    Select::make('category_id')
                                                                        ->relationship('category', 'name')
                                                                        ->placeholder('No category selected')
                                                                        ->reactive(),
                                                                ] : [],
                                                                ...($get('payee_button') === ['payee'] ? [
                                                                    Select::make('payee_id')
                                                                        ->relationship('payee', 'name')
                                                                        ->placeholder('No payee selected')
                                                                        ->label('Payee'),
                                                                ] : []),
                                                                ...($get('note_button') === ['notes'] ? [
                                                                    TextInput::make('notes')
                                                                        ->label('Notes'),
                                                                ] : []),
                                                                ...($get('tag_button') === ['tags'] ? [
                                                                    Select::make('tag_id')
                                                                        ->relationship('tag', 'name')
                                                                        ->placeholder('No tag selected')
                                                                        ->reactive(),
                                                                ] : []),
                                                            ];
                                                        }),
                                                    ];
                                                }),
                                            ])->reorderable(false),
                                    ],
                                    'reviewed' => [
                                        ToggleButtons::make('reviewed')
                                            ->label('Reviewed')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    'send_me_email' => [
                                        ToggleButtons::make('send_me_email')
                                            ->label('Enter email')
                                            ->options([
                                                'true' => 'Yes',
                                                'false' => 'No',
                                            ])
                                            ->inline(),
                                    ],
                                    default => [],
                                };
                            }),
                    ])
                    ->reorderable(false)
                    ->maxItems(13),
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
                            if ($getIfAction?->payee) {
                                $response .= ' payee name = '.$getIfAction->payee->payeeName->name.'<br>';
                            }

                            if ($getIfAction?->category) {
                                $response .= ' matches category = '.$getIfAction->category->name.'<br>';
                            }

                            if ($getIfAction?->note) {
                                $response .= ' matches notes = '.$getIfAction->note->note.'<br>';
                            }

                            if ($getIfAction?->day) {
                                $response .= ' matches day = '.$getIfAction->day->day.'<br>';
                            }

                            if ($getIfAction?->account) {
                                $response .= ' in account = '.$getIfAction->account->name.'<br>';
                            }

                            if ($getIfAction?->amount) {
                                $response .= ' amount = '.$getIfAction->amount->amount.'<br>';
                            }
                        }

                        return $response;
                    })
                    ->html(),
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

                            if ($getThenAction['reviewed']) {
                                $response .= ' '.'reviewed = '.' '.$getThenAction['reviewed'].'<br>';
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
