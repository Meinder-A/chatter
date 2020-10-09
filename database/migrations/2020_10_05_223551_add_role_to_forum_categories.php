<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleToForumCategories extends Migration
{
    public function up()
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->json('roles')->nullable();
        });
    }

    public function down()
    {
        Schema::table('forum_categories', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
}
