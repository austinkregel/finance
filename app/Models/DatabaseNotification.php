<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

class DatabaseNotification extends \Illuminate\Notifications\DatabaseNotification implements AbstractEloquentModel
{
    use AbstractModelTrait;

    public function getAbstractAllowedFilters(): array
    {
        return $this->fillable;
    }

    public function getAbstractAllowedRelationships(): array
    {
        return [];
    }

    public function getAbstractAllowedSorts(): array
    {
        return ['created_at'];
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
        return [];
    }

    public function getValidationUpdateRules(): array
    {
        return [];
    }
}
