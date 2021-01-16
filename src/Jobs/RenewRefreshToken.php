<?php

namespace Gcaimarket\Douyin\Jobs;

use Gcaimarket\Douyin\Models\DouyinAuthUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jncinet\LaravelByteDance\ByteDance;

class RenewRefreshToken implements ShouldQueue
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->status > 6 && $this->user->status < 1) {
            return null;
        }

        $data = ByteDance::DouYin()->OAuth()->access_token()->renewRefreshToken($this->user->refresh_token);

        if ($data['data']['error_code'] == 0) {
            $this->user->refresh_token = $data['data']['refresh_token'];
            $this->user->refresh_expires_in = now()->addSeconds($data['data']['expires_in'] - 30);
            $this->user->status += 1;
            // 更新成功后，再次分发
            if ($this->user->save()) {
                self::dispatch($this->user)->delay($this->user->refresh_expires_in);
            }
        } else {
            $data->status = 0;
            $data->save();
        }
    }
}
