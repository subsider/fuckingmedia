<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('mbid')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->index();
            $table->string('real_name')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('on_tour')->nullable();
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
        Schema::dropIfExists('artists');
    }
}
