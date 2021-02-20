<?php

namespace App\Models;

use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * Class Transaction.
 *
 * @package namespace App\Models;
 * @property-read \App\Models\Account $account
 * @property-read \App\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property int $id
 * @property string|null $name
 * @property float $amount
 * @property string|null $account_id
 * @property Carbon $date
 * @property bool $pending
 * @property string|null $transaction_id
 * @property string|null $transaction_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction wherePending($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereUpdatedAt($value)
 * @property int|null $category_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereCategoryId($value)
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction after($start)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction notHas($relation)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction service($relation)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withoutFeesOrTransfers()
 * @mixin \Eloquent
 * @property int $is_subscription
 * @property int $is_possible_subscription
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereIsPossibleSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereIsSubscription($value)
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction between($startDate, $endDate)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction has($relation)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction noIncome($amount)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction null($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withFeesAndTransfers()
 * @property \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withAllTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withAnyTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction withAnyTagsOfAnyType($tags)
 * @property mixed $tag
 */
class Transaction extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait, HasTags, BelongsToThrough, HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    public $casts = [
        'pending' => 'bool',
        'amount' => 'float',
        'date' => 'datetime:Y-m-d',
        'is_subscription' => 'bool',
        'is_possible_subscription' => 'bool',
        'data' => 'json',
    ];

    public function getTagAttribute()
    {
        if (array_key_exists('tag', $this->attributes)) {
            return $this->attributes['tag'];
        }

        return null;
    }

    public function setTagAttribute($value): void
    {
        $this->attributes['tag'] = $value;
    }

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    public function scopeAccountsIn(Builder $query, ...$accounts): Builder
    {
        return $query->whereIn('account_id', $accounts);
    }

    public function scopeAfter(Builder $query, $start): Builder
    {
        return $query->where('date', '>=', Carbon::parse($start));
    }

    public function scopeBetween(Builder $query, $startDate, $endDate): Builder
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return $query->where('date', '>=', $start)
            ->where('date', '<=', $end);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function user()
    {
        return $this->hasOneDeep(User::class, [Account::class, AccessToken::class], [
            'id',
            'id',
            'id',
        ], [
            'id',
            'access_token_id',
            'user_id'
        ]);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_transaction', 'transaction_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'category_id', 'category_id');
    }

    public function scopeWithoutFeesOrTransfers($query)
    {
        return $query->where(\DB::raw('LOWER(name)'), 'not like', '%transfer%')
            ->where(\DB::raw('LOWER(name)'), 'not like', '%interest%')
            ->where(\DB::raw('LOWER(name)'), 'not like', '%atm surcharge%')
            ->where(\DB::raw('LOWER(name)'), 'not like', '%FEE%');
    }

    public function scopeWithFeesAndTransfers($query)
    {
        return $query->where(\DB::raw('LOWER(name)'), 'like', '%transfer%')
            ->where(\DB::raw('LOWER(name)'), 'like', '%interest%')
            ->where(\DB::raw('LOWER(name)'), 'like', '%atm surcharge%')
            ->where(\DB::raw('LOWER(name)'), 'like', '%fee%');
    }

    public function scopeNoIncome(Builder $query, $amount)
    {
        return $query->where('amount', '>', 0);
    }

    public function getAbstractAllowedFilters(): array
    {
        return [
            'account_id',
            'amount',
            'date',
            'name',
            'pending',
            'transaction_id',
            'transaction_type',
            'category_id',
            'is_possible_subscription',
            'is_subscription',
            AllowedFilter::scope('service'),
            AllowedFilter::scope('after'),
            AllowedFilter::scope('withoutFeesOrTransfers'),
            AllowedFilter::scope('withFeesAndTransfers'),
            AllowedFilter::scope('no_income'),
            AllowedFilter::scope('between'),
            'subscription.type',
            AllowedFilter::scope('notHas'),
            AllowedFilter::scope('has'),
            AllowedFilter::scope('null'),
            AllowedFilter::scope('withFeesAndTransfers'),
            AllowedFilter::scope('accountsIn')
        ];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['account', 'categories', 'category', 'tags'];
    }

    public function getAbstractAllowedSorts(): array
    {
        return [
            'account_id',
            'amount',
            'date',
            'name',
            'pending',
            'transaction_id',
            'transaction_type',
            'category_id',
            'is_possible_subscription',
            'is_subscription',
        ];
    }

    public function getAbstractAllowedFields(): array
    {
        return [
            'account_id',
            'amount',
            'date',
            'name',
            'pending',
            'transaction_id',
            'transaction_type',
            'category_id',
            'is_possible_subscription',
            'is_subscription',
        ];
    }

    public function getAbstractSearchableFields(): array
    {
        return ['name'];
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
