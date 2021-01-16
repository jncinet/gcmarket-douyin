<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDouyinAuthUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('douyin_auth_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uri')->nullable();
            $table->string('open_id');
            $table->string('access_token');
            $table->string('refresh_token');
            $table->string('scope');
            $table->timestamp('expires_in')->comment('access_token有效期');
            $table->timestamp('refresh_expires_in')->comment('refresh_token有效期');
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
        Schema::dropIfExists('douyin_auth_users');
    }
}
