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
        $url = route('api.douyin.get_access');
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
     * {"data":{
     * "access_token":"act.b28c5941b946c48f9efd63cb881b6971rbJh5kVhXIs5QqzdWKG56XqoYLxf",
     * "captcha":"","desc_url":"","description":"","error_code":0,
     * "expires_in":1296000,
     * "open_id":"4982db89-baed-4f23-93fc-ff4a936bbf0b",
     * "refresh_expires_in":2592000,
     * "refresh_token":"rft.bf9fa5364f3c97539009e57058393dd2RF89z0O1GJgrKsylA3LbyvHCtirQ",
     * "scope":"user_info,video.comment,video.create,video.data,video.list,video.delete,enterprise.data,enterprise.im"
     * },"message":"success"}
     */
    public function getAccessToken(Request $request)
    {
        $code = $request->get('code');
        $access_data = ByteDance::DouYin()->OAuth()->access_token()->get($code);
//        $access_data = [
//            'data' => [
//                'error_code' => 0,
//                'access_token' => 'act.b28c5941b946c48f9efd63cb881b6971rbJh5kVhXIs5QqzdWKG56XqoYLxf',
//                'refresh_token' => 'rft.bf9fa5364f3c97539009e57058393dd2RF89z0O1GJgrKsylA3LbyvHCtirQ',
//                'scope' => 'user_info,video.comment,video.create,video.data,video.list,video.delete,enterprise.data,enterprise.im',
//                'open_id' => '4982db89-baed-4f23-93fc-ff4a936bbf0b',
//                'expires_in' => 1296000,
//                'refresh_expires_in' => 2592000,
//            ]
//        ];
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