<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $table = 'inscriptions';

    protected $fillable = [
        'id_etudiant',
        'id_cours',
        'date_inscription',
        'statut',
        'progression'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'id_etudiant');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours');
    }
}