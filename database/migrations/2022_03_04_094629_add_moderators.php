<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // laravel non permette di modificare colonne enum
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `subscription` ENUM ('free', 'premium', 'mod') NOT NULL DEFAULT 'free';");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `subscription` ENUM ('free', 'premium') NOT NULL DEFAULT 'free';");
    }
};
