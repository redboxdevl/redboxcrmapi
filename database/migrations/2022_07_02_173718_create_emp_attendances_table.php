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
        Schema::create('emp_attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('emp_id')->constrained('employees');
            $table->foreignId('machine_id')->constrained('emp_machine_attendances');
            $table->string('location_lat', 200)->nullable();
            $table->string('location_long', 200)->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->string('attendance_date', 200)->nullable();
            $table->string('checkin', 200)->nullable();
            $table->string('checkout', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_attendances');
    }
};
