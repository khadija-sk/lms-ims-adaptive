<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reponses', function (Blueprint $table) {
            $table->id();
            $table->text('texte_reponse');
            $table->boolean('est_correcte')->default(false);
            $table->foreignId('id_question')->constrained('questions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reponses');
    }
};