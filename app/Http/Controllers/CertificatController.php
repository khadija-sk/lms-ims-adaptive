<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Resultat;
use App\Models\Quiz;
use App\Models\Cours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CertificatController extends Controller
{
    // Générer un certificat après validation des conditions
    public function generer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cours' => 'required|exists:cours,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $idCours = $request->id_cours;

        // Vérifier si l'étudiant est inscrit au cours
        $estInscrit = $user->inscriptions()->where('id_cours', $idCours)->exists();
        if (!$estInscrit) {
            return response()->json(['message' => 'Vous n\'êtes pas inscrit à ce cours'], 403);
        }

        // Récupérer tous les quiz du cours
        $quizIds = Quiz::where('id_cours', $idCours)->pluck('id');
        
        // Vérifier si l'étudiant a réussi tous les quiz du cours
        $resultats = Resultat::where('id_etudiant', $user->id)
            ->whereIn('id_quiz', $quizIds)
            ->get();

        $quizReussis = $resultats->where('reussi', true)->count();
        $totalQuiz = $quizIds->count();

        if ($totalQuiz == 0) {
            return response()->json(['message' => 'Ce cours n\'a pas de quiz', 'certificat' => null], 200);
        }

        if ($quizReussis < $totalQuiz) {
            return response()->json([
                'message' => 'Vous devez réussir tous les quiz du cours pour obtenir un certificat',
                'quiz_reussis' => $quizReussis,
                'total_quiz' => $totalQuiz
            ], 403);
        }

        // Vérifier si un certificat existe déjà
        $certificatExistant = Certificat::where('id_etudiant', $user->id)
            ->where('id_cours', $idCours)
            ->first();

        if ($certificatExistant) {
            return response()->json([
                'message' => 'Certificat déjà généré',
                'certificat' => $certificatExistant
            ], 200);
        }

        // Générer un nouveau certificat
        $codeUnique = 'CERT-' . strtoupper(uniqid()) . '-' . $user->id . '-' . $idCours;
        
        $certificat = Certificat::create([
            'code_unique' => $codeUnique,
            'date_delivrance' => now()->toDateString(),
            'id_etudiant' => $user->id,
            'id_cours' => $idCours
        ]);

        return response()->json([
            'message' => 'Certificat généré avec succès',
            'certificat' => $certificat
        ], 201);
    }

    // Récupérer tous les certificats d'un étudiant
    public function getCertificatsEtudiant($id)
    {
        $certificats = Certificat::with('cours')
            ->where('id_etudiant', $id)
            ->get();

        return response()->json(['certificats' => $certificats]);
    }

    // Récupérer un certificat par son code (public)
    public function verifier($code)
    {
        $certificat = Certificat::with(['etudiant', 'cours'])
            ->where('code_unique', $code)
            ->first();

        if (!$certificat) {
            return response()->json(['message' => 'Certificat invalide'], 404);
        }

        return response()->json([
            'success' => true,
            'certificat' => [
                'code' => $certificat->code_unique,
                'etudiant' => $certificat->etudiant->nom . ' ' . $certificat->etudiant->prenom,
                'cours' => $certificat->cours->titre,
                'date_delivrance' => $certificat->date_delivrance
            ]
        ]);
    }

    // Télécharger le certificat (simulation)
    public function telecharger($id)
    {
        $certificat = Certificat::with(['etudiant', 'cours'])->find($id);
        
        if (!$certificat) {
            return response()->json(['message' => 'Certificat non trouvé'], 404);
        }

        $user = auth()->user();
        if ($user->id != $certificat->id_etudiant && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json([
            'message' => 'Certificat disponible',
            'certificat' => [
                'code' => $certificat->code_unique,
                'etudiant' => $certificat->etudiant->nom . ' ' . $certificat->etudiant->prenom,
                'cours' => $certificat->cours->titre,
                'date_delivrance' => $certificat->date_delivrance
            ]
        ]);
    }
}