<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
        });

        Schema::create('posts_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->unique(['post_id', 'category_id']);
        });

        Schema::create('favorite_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->unique(['user_id', 'category_id']);
        });


    }

    public function down()
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('posts_categories');
        Schema::dropIfExists('favorite_categories');
    }
};
