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
        Schema::create('emp_terminations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('emp_id')->constrained('employees');
            $table->string('subject', 200)->nullable();
            $table->string('type', 200)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('notice_date', 200)->nullable();
            $table->string('termination_date', 200)->nullable();
            $table->foreignId('termination_by')->constrained('emp_terminations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_terminations');
    }
};
