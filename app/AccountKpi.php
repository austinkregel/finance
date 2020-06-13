<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\AccountKpi
 *
 * @property int $id
 * @property string $date
 * @property string $account_id
 * @property float $balance
 * @property float $available
 * @property int $total_transactions_today
 * @property int $total_subscriptions_today
 * @property int $total_bills_today
 * @property int $total_spends_today
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereTotalBillsToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereTotalSpendsToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereTotalSubscriptionsToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereTotalTransactionsToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\AccountKpi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountKpi extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    public $fillable = [
        'account_id',
        'balance',
        'available',
        'total_transactions_today',
        'total_subscriptions_today',
        'total_bills_today',
        'total_spends_today',
        'date'
    ];

    public function getAbstractAllowedFilters(): array
    {
        return $this->fillable;
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['account'];
    }

    public function getAbstractAllowedSorts(): array
    {
        return $this->fillable;
    }

    public function getAbstractAllowedFields(): array
    {
        return $this->fillable;
    }

    public function getAbstractSearchableFields(): array
    {
        return $this->fillable;
    }

    public function getValidationCreateRules(): array
    {
        // TODO: Implement getValidationCreateRules() method.
    }

    public function getValidationUpdateRules(): array
    {
        // TODO: Implement getValidationUpdateRules() method.
    }
}
