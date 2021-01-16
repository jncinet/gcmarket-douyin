<?php

namespace Gcaimarket\Douyin\Models;

use Illuminate\Database\Eloquent\Model;

class DouyinAuthUser extends Model
{
    protected $fillable = [
        'uri', 'open_id', 'access_token', 'refresh_token', 'scope', 'expires_in', 'refresh_expires_in', 'status'
    ];

    protected $casts = [
        'expires_in' => 'datetime:Y-m-d H:i:s',
        'refresh_expires_in' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}