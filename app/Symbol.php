<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    use SoftDeletes;

    protected $table = 'symbols';

    protected $fillable = [
        'icon', 'info'
    ];
}
