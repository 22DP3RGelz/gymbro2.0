<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workout_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('monday')->default(false);
            $table->boolean('monday_completed')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('tuesday_completed')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('wednesday_completed')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('thursday_completed')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('friday_completed')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('saturday_completed')->default(false);
            $table->boolean('sunday')->default(false);
            $table->boolean('sunday_completed')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_schedules');
    }
};
