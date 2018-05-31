<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    protected $table = 'symbols';

    protected $fillable = [
        'icon', 'info'
    ];
}
