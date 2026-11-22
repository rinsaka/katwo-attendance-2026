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
        Schema::create('mailinfos', function (Blueprint $table) {
            $table->id();
            $table->string('key', 30)->unique();  // sqlite では文字列の長さは指定しても意味がない
            $table->string('mailinfo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailinfos');
    }
};
