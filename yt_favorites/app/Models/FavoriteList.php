<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteList extends Model
{

    public $timestamps=false;
    protected $fillable = [
        'user_id',
        'video_id',
        'added_at',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(related: User::class);
    }
    
}
