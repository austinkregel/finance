<?php

namespace App;

use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use RRule\RRule;
use Spatie\QueryBuilder\AllowedFilter;
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
    use AbstractModelTrait, BelongsToThrough, HasTags {
        getTagClassName as oldTagName;
    }

    public $guarded = [];

    protected $casts = [
        'started_at' => 'datetime',
        'breached_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function ($budget): void {
            if (empty($budget->user_id) && auth()->check()) {
                $budget->user_id = auth()->id();
            }
        });
    }

    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    public function scopeTotalSpends(Builder $query, $startingPeriod, int $userId): void
    {
        $query->addSelect([
            'total_spend' => Transaction::crossJoin('taggables', 'taggables.taggable_id', '=', 'transactions.id')
                ->whereIn('taggables.tag_id', $this->tags()->select('id'))
                ->selectRaw('sum(amount) as amount')
                ->where('taggables.taggable_type', '=', Transaction::class)
                ->whereIn(
                    'transactions.account_id',
                    Account::select('account_id')
                    ->whereIn('access_token_id', AccessToken::select('id')
                        ->where('user_id', $userId))
                )
                ->where('date', '>=', $startingPeriod)
        ]);
    }

    public function getValidationCreateRules(): array
    {
        return [
            'name' => 'required',
            'amount' => 'required',
            'frequency' => 'required',
            'interval' => 'required',
            'started_at' => 'required',
            'count' => 'nullable',
        ];
    }

    public function getValidationUpdateRules(): array
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
            'started_at' => 'date|nullable',
            'count' => 'numeric|nullable',
        ];
    }

    public function getAbstractAllowedFilters(): array
    {
        return [
            AllowedFilter::scope('totalSpends'),
        ];
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['tags.transactions'];
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

    public function getStartOfCurrentPeriod(): Carbon
    {
        return Carbon::parse($this->getRule()->getOccurrencesBefore(now(), true, 1)[0]);
    }

    public function getEndOfCurrentPeriod(): Carbon
    {
        return Carbon::parse($this->getRule()->getOccurrencesAfter(now(), false, 1)[0]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
