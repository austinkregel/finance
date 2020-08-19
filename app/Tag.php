<?php

namespace App;

use App\Contracts\ConditionableContract;
use App\Models\Traits\Conditionable;
use App\Models\Transaction;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\Tags\Tag as SpatieTag;

/**
 * App\Group
 *
 * @property int $id
 * @property array $name
 * @property array $slug
 * @property string|null $type
 * @property int|null $order_column
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Condition[] $conditionals
 * @property-read int|null $conditionals_count
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Tags\Tag containing($name, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Tags\Tag ordered($direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereOrderColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\Tags\Tag withType($type = null)
 * @mixin \Eloquent
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property int $must_all_conditions_pass
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Tag whereMustAllConditionsPass($value)
 */
class Tag extends SpatieTag implements AbstractEloquentModel, ConditionableContract
{
    use Conditionable, AbstractModelTrait;
    public $guarded = [];

    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->user_id)) {
                $tag->user_id = auth()->check() ? auth()->id() : 1;
            }
        });
    }

    public function getValidationCreateRules(): array
    {
        // TODO: Implement getValidationCreateRules() method.
    }

    public function getValidationUpdateRules(): array
    {
        // TODO: Implement getValidationUpdateRules() method.
    }

    public function getAbstractAllowedFilters(): array
    {
        return ['user_id', 'name', 'order_column', 'type'];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['conditionals'];
    }

    public function getAbstractAllowedSorts(): array
    {
        return ['user_id', 'name', 'order_column', 'type'];
    }

    public function getAbstractAllowedFields(): array
    {
        return ['user_id', 'name', 'order_column', 'type'];
    }

    public function getAbstractSearchableFields(): array
    {
        return ['user_id', 'name', 'order_column', 'type'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function transactions()
    {
        return $this->morphedByMany(Transaction::class, 'taggable');
    }
}
