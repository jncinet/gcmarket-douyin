<?php

namespace Gcaimarket\Douyin\Jobs;

use Gcaimarket\Douyin\Models\DouyinAuthUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jncinet\LaravelByteDance\ByteDance;

class RenewAccessToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @param DouyinAuthUser $user
     * @return void
     */
    public function __construct(DouyinAuthUser $user)
    {
        $this->user = $user;
    }

    /**
     * 提交5秒刷新access_token，如果更新成功，会再次触发刷新任务
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->status > 6 && $this->user->status < 1) {
            return null;
        }

        $data = ByteDance::DouYin()->OAuth()->access_token()->refreshToken($this->user->refresh_token);

        if ($data['data']['error_code'] == 0) {
            $this->user->open_id = $data['data']['open_id'];
            $this->user->access_token = $data['data']['access_token'];
            $this->user->refresh_token = $data['data']['refresh_token'];
            $this->user->scope = $data['data']['scope'];
            $this->user->expires_in = now()->addSeconds($data['data']['expires_in'] - 30);
            $this->user->refresh_expires_in = now()->addSeconds($data['data']['refresh_expires_in'] - 30);
            // 更新成功后，再次分发
            if ($this->user->save()) {
                self::dispatch($this->user)->delay($this->user->expires_in);
            }
        } else {
            $data->status = 0;
            $data->save();
        }
    }
}
