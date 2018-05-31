<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'name', 'brand', 'color', 'material', 'style', 'symbols', 'price', 'image', 'categoryID'
    ];

    public function symbols() {
        return $this->belongsToMany('App\Symbol', 'symbol_id', 'item_id', 'symbol_id');
    }
}
