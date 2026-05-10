<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resultats', function (Blueprint $table) {
            $table->id();
            $table->float('score');
            $table->dateTime('date_passage');
            $table->boolean('reussi')->default(false);
            $table->foreignId('id_etudiant')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('id_quiz')->constrained('quiz')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultats');
    }
};
