<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string("first_name")->default('');
            $table->string("last_name")->default('');
            $table->string("email")->nullable(false)->unique();
            $table->string("password")->nullable(false);
            $table->string("api_token", 64)->nullable(false)->unique();
            $table->string("profile_pic")->default('');
            $table->enum("subscription", ["free", "premium"])->nullable(false);
            $table->timestamps();
        });

        Schema::create("posts", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->nullable(false);
            $table->string("title")->nullable(false);
            $table->string("content")->nullable(false);
            $table->timestamps();

            $table->foreign(["user_id"])->references("id")->on("users");
        });

        Schema::create("comments", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id")->nullable(false);
            $table->unsignedBigInteger("post_id")->nullable(false);
            $table->string("content")->nullable(false);
            $table->timestamps();

            $table->foreign(["user_id"])->references("id")->on("users");
            $table->foreign(["post_id"])->references("id")->on("posts");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('users');
    }
};
