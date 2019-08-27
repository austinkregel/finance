<?php

namespace App\Models;

use App\BillName;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use Spatie\QueryBuilder\Filter;

/**
 * Class Transaction.
 *
 * @package namespace App\Models;
 * @property-read \App\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read string $start
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
 * @property-read \App\BillName|null $billName
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereIsPossibleSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction whereIsSubscription($value)
 */
class Transaction extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'account_id',
        'amount',
        'date',
        'name',
        'pending',
        'transaction_id',
        'transaction_type',
        'category_id',
        'is_subscription',
        'is_possible_subscription',
    ];

    /**
     * @var array
     */
    public $casts = [
        'pending' => 'bool',
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_subscription' => 'bool',
        'is_possible_subscription' => 'bool',
    ];

    /**
     * @var array
     */
    public $appends = [
        'start',
    ];

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

    public function scopeNotHas(Builder $query, $relation): Builder
    {
        return $query->doesntHave($relation);
    }

    public function scopeHas(Builder $query, $relation): Builder
    {
        return $query->doesntHave($relation);
    }

    public function scopeNull(Builder $query, ...$relations): Builder
    {
        foreach ($relations as $relation) {
            $query->whereNull($relation);
        }

        return $query;
    }

    public function scopeService(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->where('is_subscription', true)
                ->orWhere('is_possible_subscription', true);
        })->orHas('billName');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_transaction', 'transaction_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'category_id', 'category_id');
    }

    public function billName()
    {
        return $this->belongsTo(BillName::class, 'name', 'name');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'name', 'transaction_name');
    }

    public function scopeWithoutFeesOrTransfers($query)
    {
        return $query->where(\DB::raw('LOWER(name)'), 'not like', '%transfer%')
            ->where(\DB::raw('LOWER(name)'),'not like', '%interest%')
            ->where(\DB::raw('LOWER(name)'),'not like', '%atm surcharge%')
            ->where(\DB::raw('LOWER(name)') ,'not like', '%FEE%');
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

    /**
     * @return string
     */
    public function getStartAttribute()
    {
        $date = Carbon::parse($this->date);

        return $date->format('M j, y');
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
            Filter::scope('service'),
            Filter::scope('after'),
            Filter::scope('withoutFeesOrTransfers'),
            Filter::scope('withFeesAndTransfers'),
            Filter::scope('no_income'),
            Filter::scope('between'),
            'subscription.type',
            Filter::scope('notHas'),
            Filter::scope('has'),
            Filter::scope('null'),
            Filter::scope('withFeesAndTransfers'),
        ];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['account', 'categories', 'category', 'bill_name'];
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
        return ['name'];
    }

    public function associateBillName()
    {
        if (!empty($this->billName)) {
            return;
        }

        $billName = BillName::where('name', $this->name)->first();

        if (empty($billName)) {
            $billName = BillName::create([
                'name' => $this->name,
                'type' => request()->get('type') ?? 'subscription',
            ]);
        }

        $this->billName()->associate($billName);

        $billName->categories()->sync($this->categories->map->id);
    }

    public function createSubscription()
    {
        if (Subscription::where('name', $this->name)->exists()) {
            return;
        }

        Subscription::create([
            'name' => $this->name,
            'transaction_name' => $this->name,
            'type' => request()->get('type') ?? 'subscription',
            'amount' => $this->amount,
            'frequency' => request()->get('frequency') ?? 'MONTHLY',
            'interval' => request()->get('interval') ?? '1',
            'started_at' => $this->date,
            'user_id' => auth()->id(),
            'account_id'=> $this->account->id,
        ]);
    }
}
