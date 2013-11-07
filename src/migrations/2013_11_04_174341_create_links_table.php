<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ka_links', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('url');

            $table->string('page_name');

            $table->unique('url');

            $table->string('linkable_type');
            $table->integer('linkable_id')->unsigned();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ka_links');
	}

}