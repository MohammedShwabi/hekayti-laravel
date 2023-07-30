<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stories_media', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('page_no');
            $table->bigInteger('story_id')->unsigned();
            $table->string('photo')->unique();
            $table->string('sound')->unique();
            $table->string('text');
            $table->string('text_no_desc');
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories_media');
    }
};
