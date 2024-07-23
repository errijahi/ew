<?php

namespace App\Models;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'payee_id',
        'notes',
        'amount',
        'status',
        'transaction_source',
        'team_id',
    ];

    protected $with = ['payee'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function getMonthlyData($month, $year)
    {
        return Transaction::get();
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recurringItem()
    {
        return $this->belongsTo(RecurringItem::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public static function aggregateTransactionValues($transactionValues, $selectedModel, $period)
    {
        $tableValues = [];
        $searchBy = '';

        foreach ($transactionValues as $transactionValue) {
            $modelData = self::getSelectedModel($selectedModel, $transactionValue);
            $modelId = $modelData['ModelValues'];
            $yearKey = $transactionValue->created_at->year;
            $monthKey = $transactionValue->created_at->format('F');
            $weekKey = $transactionValue->created_at->week;
            $dayKey = $transactionValue->created_at->day;
            $searchBy = $modelData['SearchBy'];

            if (empty($modelId)) {
                continue;
            }

            $amount = (string) $transactionValue->amount;

            switch ($period) {
                case 'year':
                    if (isset($tableValues[$modelId][$yearKey])) {
                        $tableValues[$modelId][$yearKey]['amount'] = (string) ($tableValues[$modelId][$yearKey]['amount'] + $transactionValue->amount);
                    } else {
                        $tableValues[$modelId][$yearKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'month':
                    if (isset($tableValues[$modelId][$yearKey][$monthKey])) {
                        $tableValues[$modelId][$yearKey][$monthKey]['amount'] = (string) ($tableValues[$modelId][$yearKey][$monthKey]['amount'] + $transactionValue->amount);
                    } else {
                        $tableValues[$modelId][$yearKey][$monthKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'week':
                    if (isset($tableValues[$modelId][$yearKey][$weekKey])) {
                        $tableValues[$modelId][$yearKey][$weekKey]['amount'] = (string) ($tableValues[$modelId][$yearKey][$weekKey]['amount'] + $transactionValue->amount);
                    } else {
                        $tableValues[$modelId][$yearKey][$weekKey] = [
                            'amount' => $amount,
                        ];
                    }
                    break;

                case 'day':
                    if (isset($tableValues[$modelId][$yearKey][$monthKey][$dayKey])) {
                        $tableValues[$modelId][$yearKey][$monthKey][$dayKey]['amount'] = (string) ($tableValues[$modelId][$yearKey][$monthKey][$dayKey]['amount'] + $transactionValue->amount);
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

    public static function getSelectedModel($selectedModel, $value)
    {
        $ModelValues = '';
        $SearchBy = '';

        if ($selectedModel[0]->getTable() === 'tags') {
            $ModelValues = $value->tag_id;
            $SearchBy = 'tag_id';
        }
        if ($selectedModel[0]->getTable() === 'categories') {
            $ModelValues = $value->category_id;
            $SearchBy = 'category_id';
        }
        if ($selectedModel[0]->getTable() === 'accounts') {
            $ModelValues = $value->account->id;
            $SearchBy = 'account_id';
        }
        if ($selectedModel[0]->getTable() === 'recurring_items') {
            $ModelValues = $value->recurringItem->id ?? '0';
            $SearchBy = 'recurring_item_id';
        }
        if ($selectedModel[0]->getTable() === 'payees') {
            $ModelValues = $value->payee->id ?? '0';
            $SearchBy = 'payee_id';
        }

        return ['ModelValues' => $ModelValues, 'SearchBy' => $SearchBy];
    }
}
