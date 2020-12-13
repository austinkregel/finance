<?php

namespace App;

use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\FiltersScope;

class Activity extends \Spatie\Activitylog\Models\Activity implements AbstractEloquentModel
{
    use AbstractModelTrait;

    public function getDescriptionAttribute($value)
    {
        try {
            return json_decode($value, false, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            return $value;
        }
    }

    public function getValidationCreateRules(): array
    {
        return [];
    }

    public function getValidationUpdateRules(): array
    {
        return [];
    }

    public function getAbstractAllowedFilters(): array
    {
        return [
            AllowedFilter::scope('inLog'),
            AllowedFilter::scope('causedBy'),
            AllowedFilter::scope('forSubject'),
            'log_name',
            'description',
            'subject_type',
            'subject_id',
            'causer_type',
            'causer_id',
            'properties',
        ];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return [
            'subject.institution',
            'causer',
        ];
    }

    public function getAbstractAllowedSorts(): array
    {
        return [
            'created_at'
        ];
    }

    public function getAbstractAllowedFields(): array
    {
        return [];
    }

    public function getAbstractSearchableFields(): array
    {
        return [];
    }
}
