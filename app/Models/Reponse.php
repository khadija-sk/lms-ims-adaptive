<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reponse extends Model
{
    protected $table = 'reponses';
    
    protected $fillable = [
        'texte_reponse',
        'est_correcte',
        'id_question'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'id_question');
    }
}