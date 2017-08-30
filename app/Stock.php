<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $fillable = [
        'exchange_id',
        'ticker',
        'last_price',
        'change',
        'chance_percent',
        'dividend',
        'yield',
    ];
}
