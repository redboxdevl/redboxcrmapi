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
        Schema::create('table_activities_log', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('agentId');
            $table->string('emp_name',255);
            $table->string('emp_email',255);
            $table->string('emp_phoneNo',255);
            $table->string('module_name',255);
            $table->string('status',255);
            $table->string('dateTime',255);
            $table->string('modifyUser',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_activities_log');
    }
};
