<?php

namespace Gcaimarket\Douyin\Controllers\Admin;

use Gcaimarket\Douyin\Models\DouyinVideoItem;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DouyinVideoItemController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '视频管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DouyinVideoItem());

        $grid->model()->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('douyin_auth_user_id', __('gc-douyin::item.douyin_auth_user_id'));
            $filter->equal('short_video_id', __('gc-douyin::item.short_video_id'));
            $filter->equal('item_id', __('gc-douyin::item.item_id'));
            $filter->equal('status', __('gc-douyin::item.status'))
                ->select(__('gc-douyin::item.status_value'));
            $filter->between('created_at', __('admin.created_at'))->datetime();
        });

        $grid->column('id', __('gc-douyin::item.id'));
        $grid->column('douyin_auth_user.uri', __('gc-douyin::item.douyin_auth_user_id'));
        $grid->column('short_video.desc', __('gc-douyin::item.short_video_id'))->limit(36);
        $grid->column('item_id', __('gc-douyin::item.item_id'));
        $grid->column('status', __('gc-douyin::item.status'))
            ->using(__('gc-douyin::item.status_value'));
        $grid->column('updated_at', __('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(DouyinVideoItem::findOrFail($id));

        $show->field('id', __('gc-douyin::item.id'));
        $show->field('douyin_auth_user_id', __('gc-douyin::item.douyin_auth_user_id'));
        $show->field('short_video_id', __('gc-douyin::item.short_video_id'));
        $show->field('item_id', __('gc-douyin::item.item_id'));
        $show->field('status', __('gc-douyin::item.status'))
            ->using(__('gc-douyin::item.status_value'));
        $show->field('created_at', __('admin.created_at'));
        $show->field('updated_at', __('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new DouyinVideoItem());

        $form->number('douyin_auth_user_id', __('gc-douyin::item.douyin_auth_user_id'))->required();
        $form->number('short_video', __('gc-douyin::item.short_video'))->required();
        $form->text('item_id', __('gc-douyin::item.item_id'));
        $form->select('status', __('gc-douyin::item.status'))
            ->default(1)
            ->options(__('gc-douyin::item.status_value'));

        return $form;
    }
}
