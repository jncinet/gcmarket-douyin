<?php

use Illuminate\Routing\Router;

// 接口
Route::group([
    'prefix' => config('qihu.douyin_prefix', 'douyin'),
    'namespace' => 'Gcaimarket\Douyin\Controllers\Api',
    'middleware' => ['api'],
    'as' => 'api.douyin.'
], function (Router $router) {
    // 读取授权二维码
    $router->get('code', 'AuthController@getCode')->name('get_code');
    // 获取open_id和access_token
    $router->get('access', 'AuthController@getAccessToken')->name('get_access_token');
});

// 后台
Route::group([
    'prefix' => config('admin.route.prefix') . '/douyin',
    'namespace' => 'Gcaimarket\Douyin\Controllers\Admin',
    'middleware' => config('admin.route.middleware'),
    'as' => 'admin.douyin.'
], function (Router $router) {
    // 配置
    $router->get('config', 'ConfigController@index')->name('config');
    $router->resource('users', 'DouyinAuthUserController', ['as' => 'auth.users']);
    $router->resource('items', 'DouyinVideoItemController', ['as' => 'video.items']);
});