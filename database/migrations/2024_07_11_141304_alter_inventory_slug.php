<?php

 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 class AlterInventorySlug extends Migration
 {
     /**
      * Run the migrations.
      *
      * @return void
      */
     public function up()
     {
         Schema::table('inventories', function (Blueprint $table) {
             $table->string('slug', 200)->nullable()->change();
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::table('inventories', function (Blueprint $table) {
             $table->string('slug', 200)->change();
         });
     }
 }