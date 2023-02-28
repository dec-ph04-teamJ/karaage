<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatoutputs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("input_id")->references('id')->on('chatinputs')->cascadeOnDelete();
            $table->foreignId("user_id")->references('id')->on('users')->cascadeOnDelete();
            $table->float("score")->nullable();
            $table->float("kanji_rate")->nullable();
            $table->float("emoji_rate")->nullable();
            $table->float("naive_bayes")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatoutputs');
    }
};
