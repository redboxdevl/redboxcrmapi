<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NotinterestedReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NotinterestedReason', function (Blueprint $table) {
            $table->id();
            $table->integer('leadid');
            $table->integer('agentid');
            $table->string('reasonTitle');
            $table->string('budgetPrice');
            $table->string('otherReason');
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
        Schema::dropIfExists('NotinterestedReason');
    }
}
