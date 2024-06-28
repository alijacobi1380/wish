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
        Schema::create('whomakefilm', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID');
            $table->integer('RID');
            $table->boolean('AdminStatus')->nullable();
            $table->boolean('CompanyStatus')->nullable();
            $table->boolean('FilmmakerStatus')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('fulladdress')->nullable();
            $table->string('zipcode')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whomakefilm');
    }
};
