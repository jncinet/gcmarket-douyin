<?php

namespace Gcaimarket\Douyin\Controllers\Admin;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class ConfigForm extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '抖音模块设置';

    public function handle(Request $request)
    {
        $data = $request->only(['byte_dance_client_key', 'byte_dance_client_secret']);

        if (app('env-editor')->setEnv($data)) {
            admin_success('更新成功');
        } else {
            admin_error('更新失败');
        }

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('byte_dance_client_key', 'client_key');
        $this->text('byte_dance_client_secret', 'client_secret');
    }

    public function data()
    {
        return [
            'byte_dance_client_key' => config('byte-dance.client_key'),
            'byte_dance_client_secret' => config('byte-dance.client_secret'),
        ];
    }
}
