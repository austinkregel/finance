<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Models\Account
 *
 * @property-read \App\Models\AccessToken $token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $account_id
 * @property int $access_token_id
 * @property string|null $mask
 * @property string|null $name
 * @property string|null $official_name
 * @property float $balance
 * @property float $available
 * @property string|null $subtype
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereMask($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereOfficialName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereSubtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Account currentUser()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
 * @property-read int|null $activities_count
 */
class Account extends Model implements AbstractEloquentModel
{
    use HasFactory;
    use AbstractModelTrait, BelongsToThrough, LogsActivity;

    protected $fillable = [
        'account_id',
        'mask',
        'name',
        'official_name',
        'balance',
        'available',
        'subtype',
        'type',
        'access_token_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope('user_filter', function (Builder $builder) {
            if (auth()->check()) {
                $builder->whereIn('access_token_id', auth()->user()->accessTokens->map->id);
            }
        });
    }

    public function scopeCurrentUser(Builder $query)
    {
        return $query->whereHas('user', function ($query): void {
            $query->where('id', auth()->id());
        });
    }

    public function token()
    {
        return $this->belongsTo(AccessToken::class, 'access_token_id', 'id', 'access_tokens');
    }

    public function institution()
    {
        return $this->belongsToThrough(Institution::class, AccessToken::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function owner()
    {
        return $this->belongsToThrough(User::class, AccessToken::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'account_users');
    }

    public function getAbstractAllowedFilters(): array
    {
        return array_merge($this->fillable, [
            AllowedFilter::scope('currentUser'),
        ]);
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['token', 'user', 'token.institution', 'institution'];
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
