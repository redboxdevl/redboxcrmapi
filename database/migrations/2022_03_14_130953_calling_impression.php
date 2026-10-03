<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CallingImpression extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CallingImpression', function (Blueprint $table) {
            $table->id();
            $table->integer('leadid');
            $table->integer('agentid');
            $table->string('impType');
            $table->datetime('impDate');
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
        Schema::dropIfExists('CallingImpression');
    }
}
