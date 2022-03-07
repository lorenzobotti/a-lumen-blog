<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('users', function(Blueprint $schema) {
            $schema->timestamp('banned_at')->nullable(true);
            $schema->softDeletes();
        });

        Schema::table('posts', function(Blueprint $schema) {
            $schema->softDeletes();
        });

        Schema::table('comments', function(Blueprint $schema) {
            $schema->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $schema) {
            $schema->dropColumn('banned_at');
            $schema->dropSoftDeletes();
        });

        Schema::table('posts', function(Blueprint $schema) {
            $schema->dropSoftDeletes();
        });

        Schema::table('comments', function(Blueprint $schema) {
            $schema->dropSoftDeletes();
        });
    }
};
