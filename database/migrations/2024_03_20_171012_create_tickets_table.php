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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('Title');
            $table->string('Status');
            $table->string('Subticket')->nullable();
            $table->integer('SenderID');
            $table->string('SenderName');
            $table->integer('ReciverID');
            $table->string('ReciverName');
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
        Schema::dropIfExists('tickets');
    }
};
