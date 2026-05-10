<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_etudiant')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('id_cours')->constrained('cours')->onDelete('cascade');
            $table->date('date_inscription');
            $table->enum('statut', ['en_cours', 'termine', 'abandonne'])->default('en_cours');
            $table->float('progression')->default(0);
            $table->timestamps();

            $table->unique(['id_etudiant', 'id_cours']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscriptions');
    }
};