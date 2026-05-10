<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    
    protected $fillable = [
        'texte',
        'type',
        'points',
        'id_quiz'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'id_quiz');
    }

    public function reponses()
    {
        return $this->hasMany(Reponse::class, 'id_question');
    }
}