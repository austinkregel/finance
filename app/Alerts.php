<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alerts extends Model
{
    public $fillable = [
        'user_id',
        'stock_id',
        'user_stocks_id',
        'target_price',
        'track_macd',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user_stock()
    {
        return $this->belongsTo(UserStocks::class);
    }
}
