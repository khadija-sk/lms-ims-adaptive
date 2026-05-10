<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultat extends Model
{
    protected $table = 'resultats';
    
    protected $fillable = [
        'score',
        'date_passage',
        'reussi',
        'id_etudiant',
        'id_quiz'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'id_etudiant');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz');
    }
}