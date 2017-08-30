<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStocks extends Model
{
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
