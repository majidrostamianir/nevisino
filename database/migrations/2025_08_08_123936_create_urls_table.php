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
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('title_tag');
            $table->string('dashed_url');
            $table->string('title_h1')->default(null);
            $table->text('meta_description')->default(null);
            $table->text('article')->default(null);
            $table->text('mini_article')->default(null);
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->boolean('in_menu')->default(false);
            $table->boolean('indexing')->default(false);
            $table->boolean('following')->default(true);
            $table->timestamps();

            $table->fullText('title');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
