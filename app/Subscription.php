<?php

namespace App;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;
use RRule\RRule;
use Spatie\QueryBuilder\Filter;

/**
 * App\Subscription
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property float $amount
 * @property string $frequency
 * @property int $interval
 * @property string $started_at
 * @property string|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $is_due_today
 * @property-read mixed $next_due_date
 */
class Subscription extends Model implements AbstractEloquentModel
{
    use AbstractModelTrait;

    protected $fillable = [
        'name',
        'type',
        'amount',
        'frequency',
        'interval',
        'started_at',
        'ended_at',
        'user_id',
        'transaction_name',
        'account_id',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    protected $appends = [
        'is_due_today',
        'next_due_date',
        'next_sort',
        'current_due_date',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'name', 'transaction_name')->orderByDesc('date');
    }

    public function fiveTransactions()
    {
        return $this->transactions()->take(5);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getIsDueTodayAttribute()
    {
        $startedAtString = $this->attributes['started_at'];
        $frequency = $this->attributes['frequency'];
        $interval = $this->attributes['interval'];
        $startedAt = $startedAtString instanceof Carbon ? $startedAtString : Carbon::parse($startedAtString);

        $rrule = [
            'FREQ' => $frequency,
            'INTERVAL' => $interval,
            'DTSTART' => $startedAt,
        ];

        if (!empty($this->ended_at)) {
            $rrule['UNTIL'] = $this->ended_at;
        }

        $rule = new RRule($rrule);

        $occurrences = $rule->getOccurrencesAfter(Carbon::now(), true, 1);

        if (empty($occurrences[0])) {
            return false;
        }

        return now()->format('Y-m-d') === $occurrences[0]->format('Y-m-d');
    }

    public function getNextDueDateAttribute()
    {
        $endedAtString = $this->attributes['ended_at'] ?? null;
        $startedAtString = $this->attributes['started_at'];

        $endedAt = empty($endedAtString) ? null : $endedAtString instanceof Carbon ? $endedAtString : Carbon::parse($endedAtString);

        $frequency = $this->attributes['frequency'];
        $interval = $this->attributes['interval'];
        $startedAt = $startedAtString instanceof Carbon ? $startedAtString : Carbon::parse($startedAtString);

        $rrule = [
            'FREQ' => $frequency,
            'INTERVAL' => $interval,
            'DTSTART' => $startedAt,
        ];

        if (!empty($endedAtString)) {
            if ($endedAt->lt(now()) && $endedAt->diffInMonths(now()) >= 1) {
                return null;
            }

            $rrule['UNTIL'] = $endedAt;
        }

        $rule = new RRule($rrule);

        $occurrences = $rule->getOccurrencesAfter(Carbon::now()->startOfMonth(), false, 1);

        return Arr::first($occurrences)->format('M j, y');
    }

    public function getCurrentDueDateAttribute()
    {
        $endedAtString = $this->attributes['ended_at'] ?? null;
        $startedAtString = $this->attributes['started_at'];

        $endedAt = empty($endedAtString) ? null : $endedAtString instanceof Carbon ? $endedAtString : Carbon::parse($endedAtString);

        $frequency = $this->attributes['frequency'];
        $interval = $this->attributes['interval'];
        $startedAt = $startedAtString instanceof Carbon ? $startedAtString : Carbon::parse($startedAtString);

        $rrule = [
            'FREQ' => $frequency,
            'INTERVAL' => $interval,
            'DTSTART' => $startedAt,
        ];

        if (!empty($endedAtString)) {
            if ($endedAt->lt(now()) && $endedAt->diffInMonths(now()) >= 1) {
                return null;
            }

            $rrule['UNTIL'] = $endedAt;
        }

        $rule = new RRule($rrule);

        $occurrences = $rule->getOccurrencesAfter(Carbon::now()->startOfMonth(), true, 1);

        return Arr::first($occurrences)->format(Carbon::ATOM);
    }

    public function getNextSortAttribute()
    {
        return Carbon::parse($this->getCurrentDueDateAttribute())->unix();
    }

    public function getAbstractAllowedFilters(): array
    {
        return array_merge($this->fillable, [
            Filter::scope('month')
        ]);
    }

    public function getAbstractAllowedRelationships(): array
    {
        return ['transactions','transactions.account','transactions.categories', 'user', 'account'];
    }

    public function getAbstractAllowedSorts(): array
    {
        return array_merge($this->fillable, ['next_due_date']);
    }

    public function getAbstractAllowedFields(): array
    {
        return $this->fillable;
    }

    public function getAbstractSearchableFields(): array
    {
        return ['name'];
    }

    public function scopeMonth(Builder $query, $month)
    {
        return $query->where(\DB::raw('MONTH(updated_at)'), $month);
    }
}
