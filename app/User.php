<?php

declare(strict_types=1);

namespace App;

use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Alert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Tags\HasTags;

/**
 * App\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AccessToken[] $accessTokens
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @property-read int|null $access_tokens_count
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $accounts
 * @property-read int|null $accounts_count
 * @property array|null $alert_channels
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $oldRelationshipTags
 * @property-read int|null $old_relationship_tags_count
 * @property \Illuminate\Database\Eloquent\Collection|\App\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAlertChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User withAllTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User withAllTagsOfAnyType($tags)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User withAnyTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User withAnyTagsOfAnyType($tags)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tag[] $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Budget[] $budgets
 * @property-read int|null $budgets_count
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable, HasTags {
        tags as oldRelationshipTags;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'alert_channels',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'alert_channels' => 'json',
    ];

    public function accessTokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function accounts()
    {
        return $this->hasManyThrough(Account::class, AccessToken::class);
    }

    public function setToken(AccessToken $token)
    {
        $this->currentToken = $token;

        return $this;
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
}
