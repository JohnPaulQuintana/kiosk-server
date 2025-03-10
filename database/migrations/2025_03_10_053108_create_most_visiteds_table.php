<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('most_visiteds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floorplan_unit_id')
            ->constrained('floorplan_units') // References the 'id' column in the 'floorplans' table by default
            ->onDelete('cascade');      // Deletes child units if the parent floorplan is deleted
            $table->bigInteger('clicked')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('most_visiteds');
    }
};
