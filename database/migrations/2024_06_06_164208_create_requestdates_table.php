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
        Schema::create('requestdates', function (Blueprint $table) {
            $table->id();
            $table->integer('WhoAddedDate');
            $table->integer('RequestID');
            $table->string('Date1');
            $table->string('Date2');
            $table->string('Date3');
            $table->string('Note')->nullable();
            $table->text('CompanyDate')->nullable();
            $table->text('ClientDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestdates');
    }
};
