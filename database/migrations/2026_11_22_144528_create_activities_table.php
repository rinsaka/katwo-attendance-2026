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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->date('act_at');
            $table->integer('place_id')->unsigned()->nullable();
            $table->integer('time_id')->unsigned()->nullable();
            $table->string('note')->nullable();
            $table->boolean('meeting')->nullable()->default(NULL);
            $table->timestamps();

            $table->foreign('place_id')
                    ->references('id')
                    ->on('places')
                    ->onDelete('cascade');

            $table->foreign('time_id')
                    ->references('id')
                    ->on('times')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
