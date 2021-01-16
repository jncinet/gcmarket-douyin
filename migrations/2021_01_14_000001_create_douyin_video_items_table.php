<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDouyinVideoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('douyin_video_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('douyin_auth_user_id')->index();
            $table->unsignedBigInteger('short_video_id')->index();
            $table->string('item_id')->nullable();
            $table->boolean('status')->default(0)->comment('状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('douyin_video_items');
    }
}
