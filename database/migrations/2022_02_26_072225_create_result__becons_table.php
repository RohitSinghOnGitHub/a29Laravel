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
        Schema::create('result__becons', function (Blueprint $table) {
            $table->id();
            $table->string('Period')->unique();
            $table->string('Color')->nullable();
            $table->string('number')->nullable();
            $table->integer('is_Fixed')->default(0);
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
        Schema::dropIfExists('result__becons');
    }
};
