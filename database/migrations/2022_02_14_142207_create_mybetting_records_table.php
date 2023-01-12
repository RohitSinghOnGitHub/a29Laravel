<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mybetting_records', function (Blueprint $table) {
            $table->id();
            $table->string('Period');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->ondelete('cascade');
            $table->double('Contract_Money', 8, 2)->default(10);
            $table->integer('Contract_Count')->default(1);
            $table->double('Delivery', 8, 2)->default(0.98);
            $table->double('Fee', 8, 2)->default(0.02);
            $table->double('Open_Price');
            $table->string('Result')->nullable();
            $table->string('Select');
            $table->string('Status')->nullable();
            $table->double('Amount', 8, 2)->default(10.0);
            $table->double('win_amount', 8, 2)->default(0.0);
            $table->string('category')->default("Parity");
            $table->timestamp('Created_Time', $precision = 0)->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mybetting_records');
    }
};
