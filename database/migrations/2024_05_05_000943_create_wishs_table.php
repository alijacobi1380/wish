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
        Schema::create('wishs', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID');
            $table->string('Title');
            $table->text('Desc');
            $table->text('MiniDesc');
            $table->integer('Category');
            $table->text('Files')->nullable();
            $table->integer('Importance')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishs');
    }
};
