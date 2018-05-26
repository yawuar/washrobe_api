<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laundry extends Model
{
    use SoftDeletes;

    protected $table = 'laundry';

    protected $fillable = [
        'user_itemID'
    ];
}
