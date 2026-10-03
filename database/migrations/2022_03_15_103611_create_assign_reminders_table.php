<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assign_reminders', function (Blueprint $table) {
            $table->id();
            $table->integer('agentid');
            $table->integer('leadid');
            $table->datetime('reminderSet');
            $table->enum('priority',['high','medium','low']);
            $table->string('reminderinfo');
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
        Schema::dropIfExists('assign_reminders');
    }
}
