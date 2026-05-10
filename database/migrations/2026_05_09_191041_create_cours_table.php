<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->enum('niveau', ['debutant', 'intermediaire', 'avance']);
            $table->string('categorie', 100);
            $table->foreignId('id_enseignant')->constrained('utilisateurs')->onDelete('cascade');
            $table->date('date_creation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cours');
    }
};