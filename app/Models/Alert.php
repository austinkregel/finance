<?php

namespace App\Models;

use App\Budget;
use App\Contracts\ConditionableContract;
use App\Models\Traits\Conditionable;
use App\Notifications\BudgetBreachedEstablishedAmountNotification;
use App\Notifications\TransactionBudgetAlertNotification;
use App\Notifications\TransactionTagAlertNotification;
use App\Tag;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Kregel\LaravelAbstract\AbstractEloquentModel;
use Kregel\LaravelAbstract\AbstractModelTrait;

/**
 * App\Models\Alert
 *
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $body
 * @property string|null $payload
 * @property array $channels
 * @property int|null $is_templated
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Condition[] $conditionals
 * @property-read int|null $conditionals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AlertLog[] $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert q($string)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereIsTemplated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $webhook_url
 * @property string|null $messaging_service_channel
 * @property array|null $events
 * @property int $must_all_conditions_pass
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereEvents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereMessagingServiceChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereMustAllConditionsPass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Alert whereWebhookUrl($value)
 */
class Alert extends Model implements AbstractEloquentModel, ConditionableContract
{
    use Conditionable, AbstractModelTrait, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'channels' => 'json',
        'events' => 'json',
        'is_templated' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(AlertLog::class);
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

    protected function notifyAbout($notification): void
    {
        $this->load('user');
        $this->user->notify($notification);
    }

    public function createNotification(Transaction $transaction): void
    {
        /** @var AlertLog $log */
        $log = $this->logs()->create([
            'triggered_by_transaction_id' => $transaction->id,
        ]);
        // do the same thing as create notification, but give access to the tag variab
        $notification = new TransactionTagAlertNotification($log);

        $this->notifyAbout($notification);
    }

    public function createNotificationWithTag(Transaction $transaction, Tag $tag): void
    {
        /** @var AlertLog $log */
        $log = $this->logs()->create([
            'triggered_by_transaction_id' => $transaction->id,
            'triggered_by_tag_id' => $tag->id,
        ]);
        // do the same thing as create notification, but give access to the tag variab
        $notification = new TransactionTagAlertNotification($log);

        $this->notifyAbout($notification);
    }
    public function createNotificationWithBudget(Transaction $transaction, Budget $budget): void
    {
        /** @var AlertLog $log */
        $log = $this->logs()->create([
            'triggered_by_transaction_id' => $transaction->id,
            'triggered_by_budget_id' => $budget->id,
        ]);
        // do the same thing as create notification, but give access to the budget variable
        $notification = new TransactionBudgetAlertNotification($log);

        $this->notifyAbout($notification);
    }

    public function createBudgetBreachNotification(Budget $budget): void
    {
        $notification = new BudgetBreachedEstablishedAmountNotification($this->logs()->create([
            'triggered_by_budget_id' => $budget->id,
        ]));

        $this->notifyAbout($notification);
    }
}
