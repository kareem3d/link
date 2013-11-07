<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CraeteArgumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ka_arguments', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('key', 50);
            $table->string('value');

            $table->smallInteger('type');

            $table->integer('link_id')->unsigned();
            $table->foreign('link_id')->references('id')->on('ka_links')->onDelete('CASCADE');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ka_arguments');
	}

}