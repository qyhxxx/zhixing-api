<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->unique();
            $table->string('session_key')->unique();
            $table->string('name')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->integer('max_score')->default(0);
            $table->integer('min_score')->default(0);
            $table->integer('province_id')->default(0);
            $table->integer('subject')->default(0);
            $table->integer('times')->default(-1);
            $table->integer('is_vip')->default(0);
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
        Schema::dropIfExists('users');
    }
}
