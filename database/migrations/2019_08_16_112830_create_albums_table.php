<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('mbid')->nullable();
            $table->string('name')->index();
            $table->string('artist_name')->index();
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('albums');
    }
}
