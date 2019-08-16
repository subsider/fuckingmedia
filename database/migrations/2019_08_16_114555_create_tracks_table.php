<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('mbid')->nullable();
            $table->string('name')->index();
            $table->string('slug')->nullable();
            $table->string('artist_name')->index();
            $table->schemalessAttributes('listeners');
            $table->schemalessAttributes('playcount');
            $table->schemalessAttributes('streamable');
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
        Schema::dropIfExists('tracks');
    }
}
