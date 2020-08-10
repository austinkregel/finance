<?php

namespace App;

use App\Models\Scopes\SummedTransactionsForBudget;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use RRule\RRule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\FiltersScope;
use Spatie\Tags\HasTags;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * App\Budget
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property float $amount
 * @property string $frequency
 * @property string $interval
 * @property \Illuminate\Support\Carbon $started_at
 * @property int|null $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget totalSpends()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget withAllTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget withAnyTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Budget withAnyTagsOfAnyType($tags)
 * @mixin \Eloquent
 */
class Budget extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait, BelongsToThrough, HasTags;

    public $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'breached_at' => 'datetime',
    ];

    public static function booted()
    {
        static::creating(function ($budget) {
            $budget->user_id = auth()->id();
        });
    }

    public function scopeTotalSpends(Builder $query)
    {
        $query->addSelect([
            'budgets.*',
            \DB::raw("CASE
           WHEN frequency='YEARLY' THEN @diff:=(YEAR(now()) - YEAR(started_at))
           WHEN frequency='MONTHLY' THEN @diff:=PERIOD_DIFF(DATE_FORMAT(now(), \"%Y%m\"), DATE_FORMAT(started_at, \"%Y%m\"))
           WHEN frequency='DAILY' THEN @diff:=DATEDIFF(now(), started_at)
           WHEN frequency='WEEKLY' THEN @diff:=ROUND(DATEDIFF(now(), started_at)/7, 0)
       END as diff"),
            \DB::raw("CASE
           WHEN frequency='YEARLY' THEN @periodStart:=if(DATE_ADD(started_at, INTERVAL @diff YEAR) < now(), DATE_ADD(started_at, INTERVAL @diff YEAR), DATE_ADD(started_at, INTERVAL @diff-1 YEAR))
           WHEN frequency='MONTHLY' THEN @periodStart:=if(DATE_ADD(started_at, INTERVAL @diff MONTH) < now(), DATE_ADD(started_at, INTERVAL @diff MONTH), DATE_ADD(started_at, INTERVAL @diff-1 MONTH))
           WHEN frequency='DAILY' THEN @periodStart:=if(DATE_ADD(started_at, INTERVAL @diff DAY) < now(), DATE_ADD(started_at, INTERVAL @diff DAY), DATE_ADD(started_at, INTERVAL @diff-1 DAY))
           WHEN frequency='WEEKLY' THEN @periodStart:=if(DATE_ADD(started_at, INTERVAL @diff WEEK) < now(), DATE_ADD(started_at, INTERVAL @diff WEEK), DATE_ADD(started_at, INTERVAL @diff-1 WEEK))
       END as period_started_at"),
            \DB::raw("CASE
           WHEN frequency='YEARLY' THEN if(DATE_ADD(started_at, INTERVAL @diff YEAR) < now(), DATE_ADD(started_at, INTERVAL @diff+1 YEAR), DATE_ADD(started_at, INTERVAL @diff YEAR))
           WHEN frequency='MONTHLY' THEN if(DATE_ADD(started_at, INTERVAL @diff MONTH) < now(), DATE_ADD(started_at, INTERVAL @diff+1 MONTH), DATE_ADD(started_at, INTERVAL @diff MONTH))
           WHEN frequency='DAILY' THEN if(DATE_ADD(started_at, INTERVAL @diff DAY) < now(), DATE_ADD(started_at, INTERVAL @diff+1 DAY), DATE_ADD(started_at, INTERVAL @diff DAY))
           WHEN frequency='WEEKLY' THEN if(DATE_ADD(started_at, INTERVAL @diff WEEK) < now(), DATE_ADD(started_at, INTERVAL @diff+1 WEEK), DATE_ADD(started_at, INTERVAL @diff WEEK))
       END as next_period"),
            \DB::raw('@userId:=user_id as user_id'),
            \DB::raw('(
           select sum(amount)
           from transactions
                    cross join taggables on taggables.tag_id in (
               select distinct taggables.tag_id
               from taggables
                        cross join tags tag
                                   on taggables.taggable_type = \'App\\\\Budget\'
                        cross join budgets b
                                   on b.id = taggables.taggable_id
               where tag.user_id = b.user_id
                        and b.user_id = cast(@userId as UNSIGNED)
           )
               and taggables.taggable_id = transactions.id
               and taggables.taggable_type = \'App\\\\Models\\\\Transaction\'
               and transactions.date >= date_format(@periodStart, "%Y-%m-%d")
               and transactions.account_id in (
                   select account_id
                   from accounts
                   where access_token_id in (
                       select id
                       from access_tokens
                       where access_tokens.user_id = cast(@userId as UNSIGNED)
                   )
               )
       ) as total_spend')
        ]);
    }

    public function getValidationCreateRules (): array
    {
        return [
            'name' => 'required',
            'amount' => 'required',
            'frequency' => 'required',
            'interval' => 'required',
            'started_at' => 'required',
            'count' => 'required',
        ];
    }

    public function getValidationUpdateRules (): array
    {
        return [
            'name' => 'string',
            'amount' => 'numeric',
            'frequency' => Rule::in([
                'MONTHLY',
                'YEARLY',
                'DAILY',
                'WEEKLY',
            ]),
            'interval' => 'numeric',
            'started_at' => 'date',
            'count' => 'numeric',
        ];
    }

    public function getAbstractAllowedFilters (): array
    {
        return [
            AllowedFilter::scope('totalSpends'),
        ];
    }

    public function getAbstractAllowedRelationships (): array
    {
        return ['tags'];
    }

    public function getAbstractAllowedSorts (): array
    {
        return [];
    }

    public function getAbstractAllowedFields (): array
    {
        return [];
    }

    public function getAbstractSearchableFields (): array
    {
        return [];
    }

    public function getRule(): RRule
    {
        return new RRule(array_merge([
            'FREQ' => $this->frequency,
            'INTERVAL' => $this->interval,
            'DTSTART' => $this->started_at,
        ], $this->count ? [
            'COUNT' => $this->count,
        ] : []));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
