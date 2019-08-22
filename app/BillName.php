<?php

namespace App;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

/**
 * App\BillName
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BillName whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BillName extends Model
{
    protected $fillable = [
        'name', 'type'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'bill_name_categories');
    }

    public function getTypeAttribute()
    {
        return ucfirst($this->attributes['type']);
    }
}
