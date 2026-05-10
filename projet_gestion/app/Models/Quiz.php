<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';
    
    protected $fillable = [
        'titre',
        'nb_questions',
        'duree_minutes',
        'note_passage',
        'id_cours'
    ];

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'id_quiz');
    }
}
