<?php

namespace Gcaimarket\Douyin\Controllers\Admin;

use Gcaimarket\Douyin\Models\DouyinAuthUser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DouyinAuthUserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '抖音授权账户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new DouyinAuthUser());

        $grid->model()->latest();

        $grid->filter(function ($filter) {
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->equal('uri', __('gc-douyin::user.uri'));
            $filter->equal('open_id', __('gc-douyin::user.open_id'));
            $filter->equal('open_id', __('gc-douyin::user.access_token'));
            $filter->equal('status', __('gc-douyin::user.status'))
                ->select(__('gc-douyin::user.status_value'));
            $filter->between('created_at', __('admin.created_at'))->datetime();
        });

        $grid->column('id', __('gc-douyin::user.id'));
        $grid->column('uri', __('gc-douyin::user.uri'));
        $grid->column('open_id', __('gc-douyin::user.open_id'));
        $grid->column('access_token', __('gc-douyin::user.access_token'));
        $grid->column('status', __('gc-douyin::user.status'))
            ->using(__('gc-douyin::user.status_value'));

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
        $show = new Show(DouyinAuthUser::findOrFail($id));

        $show->field('id', __('gc-douyin::user.id'));
        $show->field('uri', __('gc-douyin::user.uri'));
        $show->field('open_id', __('gc-douyin::user.open_id'));
        $show->field('access_token', __('gc-douyin::user.access_token'));
        $show->field('refresh_token', __('gc-douyin::user.refresh_token'));
        $show->field('scope', __('gc-douyin::user.scope'));
        $show->field('expires_in', __('gc-douyin::user.expires_in'));
        $show->field('refresh_expires_in', __('gc-douyin::user.refresh_expires_in'));
        $show->field('status', __('gc-douyin::user.status'))
            ->using(__('gc-douyin::user.status_value'));
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
        $form = new Form(new DouyinAuthUser());

        $form->text('uri', __('gc-douyin::user.uri'));
        $form->text('open_id', __('gc-douyin::user.open_id'));
        $form->text('access_token', __('gc-douyin::user.access_token'));
        $form->text('refresh_token', __('gc-douyin::user.refresh_token'));
        $form->text('scope', __('gc-douyin::user.scope'));
        $form->datetime('expires_in', __('gc-douyin::user.expires_in'));
        $form->datetime('refresh_expires_in', __('gc-douyin::user.refresh_expires_in'));
        $form->select('status', __('gc-douyin::user.status'))
            ->default(1)
            ->options(__('gc-douyin::user.status_value'));

        return $form;
    }
}
