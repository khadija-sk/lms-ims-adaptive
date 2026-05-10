<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'role'
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    // Relations pour les cours
public function coursEnseignes()
{
    return $this->hasMany(Cours::class, 'id_enseignant');
}

public function inscriptions()
{
    return $this->hasMany(Inscription::class, 'id_etudiant');
}
public function certificats()
{
    return $this->hasMany(Certificat::class, 'id_etudiant');
}
}