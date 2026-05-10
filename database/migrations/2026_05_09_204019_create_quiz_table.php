<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200);
            $table->integer('nb_questions');
            $table->integer('duree_minutes');
            $table->float('note_passage')->default(60);
            $table->foreignId('id_cours')->constrained('cours')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz');
    }
};