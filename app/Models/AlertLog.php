<?php
declare(strict_types=1);

namespace App\Models;

use App\Budget;
use App\Tag;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AlertLog
 *
 * @property int $id
 * @property int $triggered_by_transaction_id
 * @property int|null $triggered_by_tag_id
 * @property int $alert_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Alert $alert
 * @property-read \App\Tag $tag
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereTriggeredByTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereTriggeredByTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AlertLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $triggered_by_budget_id
 * @property-read Budget|null $budget
 * @method static \Illuminate\Database\Eloquent\Builder|AlertLog whereTriggeredByBudgetId($value)
 */
class AlertLog extends Model
{
    protected $guarded = [];

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'triggered_by_tag_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'triggered_by_transaction_id');
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'triggered_by_budget_id');
    }
}
