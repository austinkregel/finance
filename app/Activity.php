<?php

namespace App;

use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * App\Activity
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity inLog($logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity q(string $string)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
            'created_at',
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
