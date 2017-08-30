<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * @return BelongsToMany
     */
    public function watchedStocks()
    {
        return $this->belongsToMany(Stock::class, 'user_stocks')->withTimestamps()->withPivot('is_watched');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function alerts()
    {
        return $this->hasMany(Alerts::class);
    }
}
