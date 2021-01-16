<?php

namespace Gcaimarket\Douyin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DouyinVideoItem extends Model
{
    protected $fillable = [
        'douyin_auth_user_id', 'short_video_id', 'item_id', 'status'
    ];

    /**
     * @return BelongsTo
     */
    public function douyin_auth_user(): BelongsTo
    {
        return $this->belongsTo('Gcaimarket\Douyin\Models\DouyinAuthUser');
    }

    /**
     * @return BelongsTo
     */
    public function short_video(): BelongsTo
    {
        return $this->belongsTo('App\Models\ShortVideo');
    }
}