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
        Schema::create('replays', function (Blueprint $table) {
            $table->id();
            $table->string('Title');
            $table->integer('SenderID');
            $table->string('SenderName');
            $table->bigInteger('TicketID')->unsigned();
            $table->foreign('TicketID')->references('id')->on('tickets')->onDelete('cascade');
            $table->text('Desc');
            $table->text('Files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replays');
    }
};
