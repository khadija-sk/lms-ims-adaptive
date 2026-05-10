<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Resultat;
use Illuminate\Http\Request;

class IAController extends Controller
{
    public function getRecommandations($idEtudiant)
    {
        $user = auth()->user();
        if ($user->id != $idEtudiant && $user->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $resultats = Resultat::where('id_etudiant', $idEtudiant)
            ->with('quiz.cours')
            ->get();

        $coursSuivis = Resultat::where('id_etudiant', $idEtudiant)
            ->with('quiz.cours')
            ->get()
            ->pluck('quiz.cours.id')
            ->unique()
            ->toArray();

        $categoriePreferee = null;

        $coursRecommandes = Cours::whereNotIn('id', $coursSuivis)
            ->with('enseignant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $scoreMoyenGlobal = $resultats->avg('score') ?? 0;

        return response()->json([
            'success' => true,
            'recommandations' => $coursRecommandes,
            'analyse' => [
                'categorie_preferee' => $categoriePreferee,
                'score_moyen_global' => round($scoreMoyenGlobal, 2),
                'cours_deja_suivis' => count($coursSuivis)
            ]
        ]);
    }
}