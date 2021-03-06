<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('subjects', function (Blueprint $table) {
         $table->increments('id');
         $table->string('codigo')->unique()->nullable(false);
         $table->string('nombre')->nullable(false);
         $table->string('descripcion')->nullable(true);
         $table->boolean('estado')->default(1)->nullable(false);
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
      Schema::dropIfExists('subjects');
   }
}
