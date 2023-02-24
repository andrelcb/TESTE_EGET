<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_tasks_assigned', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_idusers')->unsigned();
            $table->foreign('users_idusers')->references('id')->on('users');
            $table->bigInteger('tasks_idtasks')->unsigned();
            $table->foreign('tasks_idtasks')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_tasks_assigned');
    }
};
