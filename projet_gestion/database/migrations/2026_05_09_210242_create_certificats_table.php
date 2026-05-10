<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificats', function (Blueprint $table) {
            $table->id();
            $table->string('code_unique', 50)->unique();
            $table->date('date_delivrance');
            $table->foreignId('id_etudiant')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('id_cours')->constrained('cours')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificats');
    }
};