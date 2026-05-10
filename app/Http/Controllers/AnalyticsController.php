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
    /**
     * KPIs pour un étudiant
     */
    public function kpisEtudiant($idEtudiant)
    {
        $user = auth()->user();
        if ($user->id != $idEtudiant && $user->role !== 'admin' && $user->role !== 'enseignant') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Récupérer tous les résultats de l'étudiant
        $resultats = Resultat::where('id_etudiant', $idEtudiant)->get();
        
        // Score moyen global
        $scoreMoyen = $resultats->avg('score') ?? 0;
        
        // Taux de réussite global
        $tauxReussite = $resultats->count() > 0 
            ? ($resultats->where('reussi', true)->count() / $resultats->count()) * 100 
            : 0;
        
        // Quiz réussis vs échoués
        $quizReussis = $resultats->where('reussi', true)->count();
        $quizEchoues = $resultats->where('reussi', false)->count();
        
        // Progression par cours
        $inscriptions = Inscription::where('id_etudiant', $idEtudiant)
            ->with('cours')
            ->get();
        
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
        
        // Dernière activité
        $derniereActivite = Resultat::where('id_etudiant', $idEtudiant)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Évolution des scores (derniers 10 quiz)
        $evolutionScores = Resultat::where('id_etudiant', $idEtudiant)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->created_at->format('Y-m-d'),
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
    }
    
    /**
     * KPIs pour un cours (pour les enseignants)
     */
    public function kpisCours($idCours)
    {
        $user = auth()->user();
        $cours = Cours::with('enseignant')->find($idCours);
        
        if (!$cours) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }
        
        if ($user->role !== 'admin' && $user->id != $cours->id_enseignant) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Inscriptions au cours
        $inscriptions = Inscription::where('id_cours', $idCours)->get();
        $totalInscrits = $inscriptions->count();
        
        // Quiz du cours
        $quizIds = Quiz::where('id_cours', $idCours)->pluck('id');
        $totalQuiz = $quizIds->count();
        
        // Résultats des étudiants
        $resultats = Resultat::whereIn('id_quiz', $quizIds)->get();
        
        // Score moyen général
        $scoreMoyen = $resultats->avg('score') ?? 0;
        
        // Taux de réussite général
        $tauxReussite = $resultats->count() > 0 
            ? ($resultats->where('reussi', true)->count() / $resultats->count()) * 100 
            : 0;
        
        // Statistiques par quiz
        $statsParQuiz = [];
        foreach ($quizIds as $quizId) {
            $quiz = Quiz::find($quizId);
            $resultatsQuiz = Resultat::where('id_quiz', $quizId)->get();
            
            $statsParQuiz[] = [
                'quiz_id' => $quizId,
                'quiz_titre' => $quiz->titre,
                'participants' => $resultatsQuiz->count(),
                'score_moyen' => round($resultatsQuiz->avg('score') ?? 0, 2),
                'taux_reussite' => $resultatsQuiz->count() > 0 
                    ? round(($resultatsQuiz->where('reussi', true)->count() / $resultatsQuiz->count()) * 100, 2)
                    : 0
            ];
        }
        
        // Top 5 des meilleurs étudiants
        $topEtudiants = Resultat::whereIn('id_quiz', $quizIds)
            ->select('id_etudiant', DB::raw('AVG(score) as moyenne'))
            ->groupBy('id_etudiant')
            ->orderBy('moyenne', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                $etudiant = Utilisateur::find($item->id_etudiant);
                return [
                    'etudiant_id' => $item->id_etudiant,
                    'nom' => $etudiant ? $etudiant->nom . ' ' . $etudiant->prenom : 'N/A',
                    'moyenne' => round($item->moyenne, 2)
                ];
            });
        
        return response()->json([
            'success' => true,
            'cours' => [
                'id' => $cours->id,
                'titre' => $cours->titre,
                'enseignant' => $cours->enseignant->nom . ' ' . $cours->enseignant->prenom
            ],
            'statistiques' => [
                'total_inscrits' => $totalInscrits,
                'total_quiz' => $totalQuiz,
                'total_soumissions' => $resultats->count(),
                'score_moyen_global' => round($scoreMoyen, 2),
                'taux_reussite_global' => round($tauxReussite, 2)
            ],
            'statistiques_par_quiz' => $statsParQuiz,
            'top_etudiants' => $topEtudiants
        ]);
    }
    
    /**
     * KPIs global pour l'admin
     */
    public function kpisGlobal()
    {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès réservé aux administrateurs'], 403);
        }
        
        // Statistiques générales
        $totalUtilisateurs = Utilisateur::count();
        $totalEtudiants = Utilisateur::where('role', 'etudiant')->count();
        $totalEnseignants = Utilisateur::where('role', 'enseignant')->count();
        $totalCours = Cours::count();
        $totalQuiz = Quiz::count();
        $totalResultats = Resultat::count();
        $totalCertificats = \App\Models\Certificat::count();
        
        // Inscriptions par mois (dernier 12 mois)
        $inscriptionsParMois = Inscription::select(
                DB::raw('YEAR(date_inscription) as annee'),
                DB::raw('MONTH(date_inscription) as mois'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->take(12)
            ->get()
            ->map(function($item) {
                return [
                    'mois' => $item->annee . '-' . str_pad($item->mois, 2, '0', STR_PAD_LEFT),
                    'total' => $item->total
                ];
            });
        
        // Top 5 des cours les plus populaires
        $topCours = Cours::withCount('inscriptions')
            ->orderBy('inscriptions_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'titre' => $item->titre,
                    'inscrits' => $item->inscriptions_count
                ];
            });
        
        // Taux de réussite global
        $tauxReussiteGlobal = $totalResultats > 0 
            ? round((Resultat::where('reussi', true)->count() / $totalResultats) * 100, 2)
            : 0;
        
        // Score moyen global
        $scoreMoyenGlobal = round(Resultat::avg('score') ?? 0, 2);
        
        return response()->json([
            'success' => true,
            'statistiques_globales' => [
                'total_utilisateurs' => $totalUtilisateurs,
                'total_etudiants' => $totalEtudiants,
                'total_enseignants' => $totalEnseignants,
                'total_cours' => $totalCours,
                'total_quiz' => $totalQuiz,
                'total_soumissions' => $totalResultats,
                'total_certificats' => $totalCertificats,
                'score_moyen_global' => $scoreMoyenGlobal,
                'taux_reussite_global' => $tauxReussiteGlobal
            ],
            'inscriptions_par_mois' => $inscriptionsParMois,
            'top_cours' => $topCours
        ]);
    }
}