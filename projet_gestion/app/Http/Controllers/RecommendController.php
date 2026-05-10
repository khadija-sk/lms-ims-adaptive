<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Resultat;
use Illuminate\Http\Request;

class RecommendController extends Controller
{
    public function getRecommandations($idEtudiant)
    {
        $coursSuivis = Resultat::where('id_etudiant', $idEtudiant)
            ->with('quiz.cours')
            ->get()
            ->pluck('quiz.cours.id')
            ->unique()
            ->toArray();

        $coursRecommandes = Cours::whereNotIn('id', $coursSuivis)
            ->with('enseignant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'recommandations' => $coursRecommandes,
            'total_cours_suivis' => count($coursSuivis)
        ]);
    }
}