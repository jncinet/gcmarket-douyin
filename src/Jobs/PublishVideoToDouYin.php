<?php

namespace Gcaimarket\Douyin\Jobs;

use App\Models\ShortVideo;
use Gcaimarket\Douyin\Models\DouyinAuthUser;
use Gcaimarket\Douyin\Models\DouyinVideoItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Jncinet\LaravelByteDance\ByteDance;
use Jncinet\LaravelByteDance\Exceptions\UploadException;

class PublishVideoToDouYin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;

    /**
     * Create a new job instance.
     *
     * @param ShortVideo $video
     * @return void
     */
    public function __construct(ShortVideo $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws UploadException
     */
    public function handle()
    {
        // 视频长度验证，必须小于15分钟
        if (!is_array($this->video->exif) || !array_key_exists('seconds', $this->video->exif)
            || $this->video->exif['seconds'] > 900) {
            return null;
        }
        // 视频地址
        $filename = $this->video->link ?: $this->video->src;
        if (empty($filename)) {
            return null;
        }
        // 发布到最新绑定的账号
        $user = DouyinAuthUser::where('status', 1)->latest()->first();
        if ($user) {
            $data = ByteDance::DouYin()->Video()
                ->create($user->open_id, $user->access_token, $filename)
                ->publish(['text' => $this->video->desc]);
            if ($data['data']['error_code'] == 0) {
                // 创建上传记录
                DouyinVideoItem::create([
                    'short_video_id' => $this->video->id,
                    'douyin_auth_user_id' => $user->id,
                    'item_id' => $data['data']['item_id'],
                    'status' => 1
                ]);
            }
        }
    }
}
