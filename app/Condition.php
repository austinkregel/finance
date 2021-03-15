<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Contracts\ConditionalsContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\Condition
 *
 * @property int $id
 * @property string|null $parameter
 * @property string|null $comparator
 * @property string|null $value
 * @property string $conditionable_type
 * @property int $conditionable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $conditionable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereComparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereConditionableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereConditionableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereParameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Condition whereValue($value)
 * @mixin \Eloquent
 */
class Condition extends Model implements AbstractEloquentModel, ConditionalsContract
{
    use HasFactory;

    use AbstractModelTrait;

    protected $guarded = [];

    public function conditionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getComparator(): string
    {
        return $this->comparator;
    }

    public function getComparatorField(): string
    {
        return $this->parameter;
    }

    /**
     * @return int|string
     */
    public function getComparatorValue()
    {
        return $this->value;
    }

    public function getAbstractAllowedFilters(): array
    {
        return [];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return [];
    }

    public function getAbstractAllowedSorts(): array
    {
        return [];
    }

    public function getAbstractAllowedFields(): array
    {
        return [];
    }

    public function getAbstractSearchableFields(): array
    {
        return [];
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
