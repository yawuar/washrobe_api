<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserItem extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $table = 'user_item';

    protected $fillable = [
        'user_itemID'
    ];
}
