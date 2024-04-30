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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('Title');
            $table->integer('UserID');
            $table->integer('Category');
            $table->bigInteger('Price');
            $table->text('Desc');
            $table->text('Pics')->nullable();
            $table->text('Status');
            $table->integer('Rate')->nullable();
            $table->integer('RateCount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
