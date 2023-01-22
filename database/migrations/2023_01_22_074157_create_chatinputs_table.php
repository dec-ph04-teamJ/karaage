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
        Schema::create('chatinputs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreign_Id("user_id")->references('id')->on('users')->onDelete('cascade');
            $table->foreign_Id("group_id")->references('id')->on('groups')->onDelete('cascade');      
            $table->text("sentence");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chatinputs');
    }
};
