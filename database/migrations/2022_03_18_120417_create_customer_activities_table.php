<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('customerId');
            $table->integer('agentid');
            $table->integer('typeId');
            $table->integer('outcomeId');
            $table->string('InterestPercentage');
            $table->string('activityDetails');
            $table->enum('actStatus',['Complete','On Going']);
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
        Schema::dropIfExists('customer_activities');
    }
}
