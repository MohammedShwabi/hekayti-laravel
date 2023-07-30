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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->unique();
            $table->string('cover_photo')->unique();
            $table->string('author',50);
            $table->tinyInteger('level');
            $table->tinyInteger('story_order');
            $table->tinyInteger('required_stars');
            $table->tinyInteger('published')->default(0);
            $table->timestamps();

            $table->unique(['level', 'story_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
