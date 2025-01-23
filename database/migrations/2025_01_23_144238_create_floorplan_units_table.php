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
        Schema::create('floorplan_units', function (Blueprint $table) {
            $table->id();
            // Add foreign key to reference the floorplans table
            $table->foreignId('floorplan_id')
            ->constrained('floorplans') // References the 'id' column in the 'floorplans' table by default
            ->onDelete('cascade');      // Deletes child units if the parent floorplan is deleted

            $table->string('unit');
            $table->string('door');
            $table->boolean('availability')->default(0);
            $table->string('old_unit')->nullable();
            $table->text('image')->nullable();
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
        Schema::dropIfExists('floorplan_units');
    }
};
