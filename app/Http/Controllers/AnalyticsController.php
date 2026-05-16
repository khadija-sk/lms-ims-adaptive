<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Quiz;
use App\Models\Resultat;
use App\Models\Utilisateur;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function kpisEtudiant($idEtudiant)
    {
        $user = auth()->user();
        if ($user->id != $idEtudiant && $user->role !== 'admin' && $user->role !== 'enseignant') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        try {
            $resultats = Resultat::where('id_etudiant', $idEtudiant)->get();
            
            $scoreMoyen = $resultats->avg('score') ?? 0;
            $tauxReussite = $resultats->count() > 0 
                ? ($resultats->where('reussi', true)->count() / $resultats->count()) * 100 
                : 0;
            $quizReussis = $resultats->where('reussi', true)->count();
            $quizEchoues = $resultats->where('reussi', false)->count();
            
            $inscriptions = Inscription::where('id_etudiant', $idEtudiant)->with('cours')->get();
            
            $progressionParCours = [];
            foreach ($inscriptions as $inscription) {
                $quizCours = Quiz::where('id_cours', $inscription->id_cours)->pluck('id');
                $resultatsCours = Resultat::where('id_etudiant', $idEtudiant)
                    ->whereIn('id_quiz', $quizCours)
                    ->get();
                
                $progressionParCours[] = [
                    'cours_id' => $inscription->id_cours,
                    'cours_titre' => $inscription->cours->titre,
                    'quiz_total' => $quizCours->count(),
                    'quiz_termines' => $resultatsCours->count(),
                    'quiz_reussis' => $resultatsCours->where('reussi', true)->count(),
                    'progression' => $quizCours->count() > 0 
                        ? ($resultatsCours->count() / $quizCours->count()) * 100 
                        : 0
                ];
            }
            
            $derniereActivite = Resultat::where('id_etudiant', $idEtudiant)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $evolutionScores = Resultat::where('id_etudiant', $idEtudiant)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->created_at ? $item->created_at->format('Y-m-d') : 'Date inconnue',
                        'score' => $item->score,
                        'quiz_titre' => $item->quiz->titre ?? 'N/A'
                    ];
                });
            
            return response()->json([
                'success' => true,
                'statistiques_globales' => [
                    'score_moyen' => round($scoreMoyen, 2),
                    'taux_reussite' => round($tauxReussite, 2),
                    'quiz_totaux' => $resultats->count(),
                    'quiz_reussis' => $quizReussis,
                    'quiz_echoues' => $quizEchoues,
                    'cours_inscrits' => $inscriptions->count()
                ],
                'progression_par_cours' => $progressionParCours,
                'evolution_scores' => $evolutionScores,
                'derniere_activite' => $derniereActivite ? $derniereActivite->created_at->diffForHumans() : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function kpisCours($idCours)
    {
        return response()->json(['message' => 'Fonctionnalité à venir']);
    }
    
    public function kpisGlobal()
    {
        return response()->json(['message' => 'Fonctionnalité à venir']);
    }
}