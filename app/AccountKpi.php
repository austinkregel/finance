<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

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
}
