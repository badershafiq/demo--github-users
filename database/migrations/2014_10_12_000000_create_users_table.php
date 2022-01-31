<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('user_name');
            $table->string('email')->nullable();
	        $table->integer('public_repos')->nullable();
	        $table->integer('followers')->nullable();
	        $table->integer('following')->nullable();
	        $table->integer('popularity')->default(0);
			$table->string('avatar_url')->nullable();
			$table->string('location')->nullable();
			$table->string('joining_date')->nullable();
			$table->longText('bio')->nullable();
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
