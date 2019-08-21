<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumTrackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_track', function (Blueprint $table) {
            $table->unsignedBigInteger('album_id');
            $table->unsignedBigInteger('track_id');
            $table->unsignedBigInteger('track_artist_id')->nullable();
            $table->unsignedInteger('position')->nullable();
            $table->unsignedInteger('duration')->nullable();

            $table->foreign('album_id')
                ->references('id')
                ->on('albums')
                ->onDelete('cascade');

            $table->foreign('track_id')
                ->references('id')
                ->on('tracks')
                ->onDelete('cascade');

            $table->foreign('track_artist_id')
                ->references('id')
                ->on('artists')
                ->onDelete('cascade');

            $table->unique([
                'album_id', 'track_id', 'track_artist_id', 'position', 'duration'
            ], 'album_track_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('album_track');
    }
}
