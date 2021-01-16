<?php

namespace Gcaimarket\Douyin\Controllers\Admin;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('抖音设置')
            ->body(new ConfigForm());
    }
}
