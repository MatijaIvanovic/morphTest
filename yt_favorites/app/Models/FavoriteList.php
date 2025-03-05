<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FavoriteList extends Model
{

    
    protected $fillable = [
        'user_id',
        'video_id',
        'title',
        'duration',
        'thumbnail_url',
        'channel_name',
        'added_at',
    ];

    public function user(): BelongsToMany {
        return $this->belongsToMany(related: User::class);
    }

    
}
