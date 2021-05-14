<?php
declare(strict_types=1);

namespace App\Models;

use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\Models\DatabaseNotification
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] all($columns = ['*'])
 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DatabaseNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
