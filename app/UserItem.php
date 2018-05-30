<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserItem extends Model
{
    use SoftDeletes;

    protected $table = 'user_item';

    protected $fillable = [
        'user_itemID'
    ];
}
