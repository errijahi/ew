<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TransactionFactory;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'payee_id',
        'notes',
        'amount',
        'status',
        'transaction_source',
        'team_id',
        'account_id',
        'tag_id',
        'category_id',
    ];

    protected $with = ['payee'];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return Collection<int, Transaction> */
    public static function getMonthlyData(string $month, string $year): Collection
    {
        return self::get();
    }

    /** @return BelongsTo<Payee, $this> */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(Payee::class);
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<RecurringItem, $this> */
    public function recurringItem(): BelongsTo
    {
        return $this->belongsTo(RecurringItem::class);
    }

    /** @return BelongsTo<Tag, $this> */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * @param  array<int, Transaction>  $transactionValues
     * @param  Collection<int, Transaction>  $selectedModel
     * @return array<string, mixed>
     */
    public static function aggregateTransactionValues(
        array $transactionValues,
        Collection $selectedModel,
        string $period
    ): array {
        $tableValues = [];
        $searchBy = '';

        foreach ($transactionValues as $transactionValue) {
            $modelData = self::getSelectedModel($selectedModel, $transactionValue);
            $modelId = $modelData['ModelValues'];
            $yearKey = $transactionValue->created_at?->year;
            $monthKey = $transactionValue->created_at?->format('F');
            $weekKey = $transactionValue->created_at?->week;
            $dayKey = $transactionValue->created_at?->day;
            $searchBy = $modelData['SearchBy'];

            if (empty($modelId)) {
                continue;
            }

            $amount = (string) $transactionValue->amount;

            switch ($period) {
                case 'year':
                    if (isset($tableValues[$modelId][$yearKey])) {
                        $tableValues[$modelId][$yearKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'month':
                    if (isset($tableValues[$modelId][$yearKey][$monthKey])) {
                        $tableValues[$modelId][$yearKey][$monthKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey][$monthKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'week':
                    if (isset($tableValues[$modelId][$yearKey][$weekKey])) {
                        $tableValues[$modelId][$yearKey][$weekKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey][$weekKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'day':
                    if (isset($tableValues[$modelId][$yearKey][$monthKey][$dayKey])) {
                        $tableValues[$modelId][$yearKey][$monthKey][$dayKey]['amount'] += $transactionValue->amount;
                    } else {
                        $tableValues[$modelId][$yearKey][$monthKey][$dayKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                default:
                    throw new InvalidArgumentException("Invalid period: $period");
            }
        }

        return ['tableValues' => $tableValues, 'searchBy' => $searchBy];
    }

    /**
     * @param  Collection<int, Transaction>  $selectedModel
     * @return array<string,int|string|null>
     */
    public static function getSelectedModel(Collection $selectedModel, Transaction $value): array
    {
        $ModelValues = '';
        $SearchBy = '';

        if ($selectedModel[0]?->getTable() === 'tags') {
            $ModelValues = $value->tag_id;
            $SearchBy = 'tag_id';
        }
        if ($selectedModel[0]?->getTable() === 'categories') {
            $ModelValues = $value->category_id;
            $SearchBy = 'category_id';
        }
        if ($selectedModel[0]?->getTable() === 'accounts') {
            $ModelValues = $value->account?->id;
            $SearchBy = 'account_id';
        }
        if ($selectedModel[0]?->getTable() === 'recurring_items') {
            $ModelValues = $value->recurringItem?->id;
            $SearchBy = 'recurring_item_id';
        }
        if ($selectedModel[0]?->getTable() === 'payees') {
            $ModelValues = $value->payee?->id;
            $SearchBy = 'payee_id';
        }

        return ['ModelValues' => $ModelValues, 'SearchBy' => $SearchBy];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model): void {
            self::applyRules($model);
        });

        static::updating(static function ($model): void {
            self::applyRules($model);
        });
    }

    public static function applyRules(Transaction $model): void
    {
        // Check if category_id exists in ifAction and matches the model's category_id
        if (IfAction::where('category_id', $model->category_id)->value('category_id') === (int) $model->category_id) {

            // Retrieve the rule_id from ifAction
            $ruleId = IfAction::where('category_id', $model->category_id)->value('rule_id');

            // Retrieve all entries from thenAction with the matched rule_id
            $actions = ThenAction::where('rule_id', $ruleId)->get();

            // Loop through the entries to check if tag_id has any value
            foreach ($actions as $action) {
                if (! empty($action->tag_id)) {
                    // You can perform your desired operations here
                    dd('Found tag_id: '.$action->tag_id, $action); // Example output for debugging
                }
            }

            // If no tag_id was found with a value, you can add any fallback logic here if needed
            dd('No entries with tag_id found');
        }
    }
}
