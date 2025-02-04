<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Music extends Model
{
    protected $table = 'musics';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'album_id',
        'isrc',
        'plataform_id',
        'trackId',
        'duration',
        'addedDate',
        'addedBy',
        'url',
    ];

    protected $casts = [
        'addedDate' => 'datetime',
    ];

    /**
     * Varios artistas podem ser donos de uma musica
     */
    public function artists()
    {
        return $this->belongsToMany(Artist::class);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function plataform()
    {
        return $this->belongsTo(Plataform::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'music_user', 'music_id', 'user_id')
            ->withTimestamps();
    }

}
