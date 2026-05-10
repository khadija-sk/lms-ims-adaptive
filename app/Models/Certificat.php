<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    protected $table = 'certificats';
    
    protected $fillable = [
        'code_unique',
        'date_delivrance',
        'id_etudiant',
        'id_cours'
    ];

    public function etudiant()
    {
        return $this->belongsTo(Utilisateur::class, 'id_etudiant');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours');
    }

    // Générer un code unique pour le certificat
    public static function genererCodeUnique($idEtudiant, $idCours)
    {
        return 'CERT-' . strtoupper(uniqid()) . '-' . $idEtudiant . '-' . $idCours;
    }
}