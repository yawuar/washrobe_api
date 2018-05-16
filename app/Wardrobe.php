<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wardrobe extends Model
{
    public $timestamps = false;
    protected $table = 'wardrobe';

    protected $fillable = [
        'name', 'gender'
    ];
}
