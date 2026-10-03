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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('emp_id')->constrained('employees');
            $table->string('trainee_name', 200)->nullable();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('training_type_id')->constrained('training_types');
            $table->string('training_title', 200)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('from_date', 200)->nullable();
            $table->string('to_date', 200)->nullable();
            $table->string('start_time', 200)->nullable();
            $table->string('end_time', 200)->nullable();
            $table->string('status', 200)->nullable();
            $table->string('attachement', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainings');
    }
};
