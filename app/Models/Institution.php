<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * Class Institution
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $institution_id
 * @property string|null $logo
 * @property string|null $site_url
 * @property array|null $products
 * @property string|null $primary_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereSiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Institution extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    protected $primaryKey = 'institution_id';

    /**
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
    public $fillable = [
        'name',
        'institution_id',
        'logo',
        'products',
        'primary_color',
        'site_url',
    ];

    protected $casts = [
        'products' => 'array',
        'institution_id' => 'string'
    ];

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
