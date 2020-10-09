<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumDiscussionTable extends Migration
{
    public function up()
    {
        Schema::create('forum_discussion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->integer('forum_category_id')->unsigned()->default('1');
            $table->string('title');
            $table->integer('user_id')->unsigned();
            $table->boolean('sticky')->default(false);
            $table->integer('views')->unsigned()->default('0');
            $table->boolean('answered')->default(0);
            $table->timestamp('last_reply_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('forum_discussion');
    }
}
