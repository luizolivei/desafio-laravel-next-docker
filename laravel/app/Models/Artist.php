<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $table = 'artists';

    protected $fillable = [
        'artist'
    ];


    public function musics()
    {
        return $this->belongsToMany(Music::class);
    }
}
