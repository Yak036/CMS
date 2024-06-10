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

        //TODO: Tabla para el historial de los usuarios
        Schema::create('historials', function (Blueprint $table) {
            $table->id();
            $table->integer('view')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('dislikes')->nullable();
            
            $table->unsignedBigInteger('user_id'); // ? Clave foránea de usuarios
            $table->timestamps();
    
            //? Definición de la clave foránea
            $table->foreign('user_id')->references('id')->on('users');
        });

        //TODO: Tabla de articulos
        Schema::create("articles", function (Blueprint $table) {
            $table->id();
            $table->string("title", 250)->unique();
            $table->longText("description");
            $table->longText("img");
            $table->string("primaryColor",45);
            $table->string("secundaryColor",45);
            $table->string("tertaryColor",45);
            $table->longText("bagraundIMG")->nullable();
            $table->integer("likes")->nullable();
            $table->integer("dislikes")->nullable();
            $table->unsignedBigInteger("user_id"); // ? Clave foranea de usuarios
            $table->timestamps();

            //? Definicion de que la clave foranea va conectada a los usuarios
            $table->foreign('user_id')->references("id")->on('users');
        });

        // TODO: Tabla comentarios
        Schema::create("comments", function (Blueprint $table) {
            $table->id();
            $table->string("description", 250);
            $table->integer("likes")->nullable();
            $table->integer("dislikes")->nullable();
            $table->unsignedBigInteger("user_id"); // ? Clave foranea de usuarios
            $table->unsignedBigInteger("article_id"); // ? Clave foranea de articulos
            $table->timestamps();

            //? Definicion de que la clave foranea va conectada a los usuarios
            $table->foreign('user_id')->references("id")->on('users');
            //? Definicion de que la clave foranea va conectada a los articulos
            $table->foreign('article_id')->references("id")->on('articles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};