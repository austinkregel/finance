<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model implements AbstractEloquentModel
{
    use HasFactory;
    use AbstractModelTrait;

    protected $guarded = [];

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
        return [];
    }

    public function getValidationUpdateRules(): array
    {
        return [];
    }
}
