<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoinWash extends Model
{
    protected $table = 'coin_wash';

    protected $fillable = [
        'name', 'street', 'number', 'zipcode', 'city', 'latitude', 'longitude'
    ];
}
