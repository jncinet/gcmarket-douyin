<?php

namespace Gcaimarket\Douyin\Controllers\Api;

use App\Http\Controllers\Controller;
use Gcaimarket\Douyin\Jobs\RenewAccessToken;
use Gcaimarket\Douyin\Jobs\RenewRefreshToken;
use Gcaimarket\Douyin\Models\DouyinAuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jncinet\LaravelByteDance\ByteDance;

class AuthController extends Controller
{
    /**
     * 会员授权，获取CODE
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getCode(Request $request)
    {
        // 授权权限
        $scope = 'user_info,video.create,video.delete,video.data,video.list,aweme.share,';
        $scope .= 'video.comment,enterprise.data,enterprise.im,incremental_authorization';
        // 接收CODE的地址
        $url = route('api.douyin.get_access_token');
        $auth = ByteDance::DouYin()->OAuth()->authorize($scope, $url);
        // 直接转到抖音授权页
        if ($request->has('url')) {
            return redirect()->away($auth->getUrl());
        }
        // 如果二维码不存在，则生成
        $filename = 'douyin/qrcode.png';
        if (!Storage::exists($filename) || $request->get('update') == 1) {
            // 生成图片文件
            Storage::put($filename, $auth->getQrCode());
        }
        // 渲染本地二维码
        return view('gc-douyin::show_qrcode');
    }

    /**
     * 获取会员access_token和open_id保存
     * 触发事件：
     * 1、定期更新access_token
     * 2、定期更新refresh_token
     *
     * @param Request $request
     * @return mixed
     */
    public function getAccessToken(Request $request)
    {
        $code = $request->get('code');
        $access_data = ByteDance::DouYin()->OAuth()->access_token()->get($code);
        if ($access_data['data']['error_code'] == 0) {
            $authUser = DouyinAuthUser::updateOrCreate(
                [
                    'open_id' => $access_data['data']['open_id'],
                    'access_token' => $access_data['data']['access_token']
                ],
                [
                    'uri' => now()->toDateTimeString(),
                    'refresh_token' => $access_data['data']['refresh_token'],
                    'scope' => $access_data['data']['scope'],
                    'expires_in' => now()->addSeconds($access_data['data']['expires_in'] - 30),
                    'refresh_expires_in' => now()->addSeconds($access_data['data']['refresh_expires_in'] - 30),
                    'status' => 1
                ]
            );
            // 刷新access_token
            RenewAccessToken::dispatch($authUser)->delay($authUser->expires_in);
            // 刷新refresh_token
            RenewRefreshToken::dispatch($authUser)->delay($authUser->refresh_expires_in);
            return view('gc-douyin::bind_success');
        }
        return $access_data;
    }
}