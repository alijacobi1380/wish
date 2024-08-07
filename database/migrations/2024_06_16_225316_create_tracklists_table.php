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
        Schema::create('tracklists', function (Blueprint $table) {
            $table->id();
            $table->string('TrackCode');
            $table->string('RID');
            $table->integer('SenderID');
            $table->integer('AdminID')->nullable();
            $table->integer('FilmmakerID')->nullable();
            $table->string('PostCompanyName');
            $table->integer('Status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracklists');
    }
};
