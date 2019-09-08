<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_content', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->index('post_id');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->longText('text');
            $table->longText('source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_content');
    }
}
