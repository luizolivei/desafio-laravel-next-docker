<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $table = 'albuns';

    /**
     * @var array
     */
    protected $fillable = [
        'album'
    ];

}
