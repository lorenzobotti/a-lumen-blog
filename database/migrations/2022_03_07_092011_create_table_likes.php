<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('comment_likes');

        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('post_id')->nullable(false);

            $table->foreign(['user_id'])->references('id')->on('users');
            $table->foreign(['post_id'])->references('id')->on('posts');

            $table->unique(['user_id', 'post_id']);
        });

        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('comment_id')->nullable(false);

            $table->foreign(['user_id'])->references('id')->on('users');
            $table->foreign(['comment_id'])->references('id')->on('comments');

            $table->unique(['user_id', 'comment_id']);
        });


    }

    public function down()
    {
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('comment_likes');
    }
};
