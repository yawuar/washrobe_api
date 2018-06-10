<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarItem extends Model
{
    use SoftDeletes;

    protected $table = 'calendar_items';

    protected $fillable = [
        'user_itemID', 'date'
    ];
}
