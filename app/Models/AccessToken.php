<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\Models\AccessToken
 *
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AccessToken whereUserId($value)
 */
class AccessToken extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    public $fillable = [
        'user_id', 'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function getAbstractAllowedFilters(): array
    {
        return $this->fillable;
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['user'];
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
