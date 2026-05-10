<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Reponse;
use App\Models\Resultat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    // Récupérer un quiz avec ses questions et réponses
    public function show($id)
    {
        $quiz = Quiz::with(['questions.reponses'])->find($id);
        if (!$quiz) {
            return response()->json(['message' => 'Quiz non trouvé'], 404);
        }
        return response()->json(['quiz' => $quiz]);
    }

    // Soumettre les réponses d'un quiz
    public function soumettre(Request $request, $idQuiz)
    {
        $quiz = Quiz::with('questions.reponses')->find($idQuiz);
        if (!$quiz) {
            return response()->json(['message' => 'Quiz non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'reponses' => 'required|array',
            'reponses.*.question_id' => 'required|exists:questions,id',
            'reponses.*.reponse_id' => 'required|exists:reponses,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reponsesEtudiant = collect($request->reponses);
        $score = 0;
        $totalPoints = 0;

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $reponseEtudiant = $reponsesEtudiant->firstWhere('question_id', $question->id);
            
            if ($reponseEtudiant) {
                $reponseCorrecte = $question->reponses->firstWhere('est_correcte', true);
                if ($reponseEtudiant['reponse_id'] == $reponseCorrecte->id) {
                    $score += $question->points;
                }
            }
        }

        $scorePourcentage = ($totalPoints > 0) ? ($score / $totalPoints) * 100 : 0;
        $reussi = $scorePourcentage >= $quiz->note_passage;

        $resultat = Resultat::create([
            'score' => $scorePourcentage,
            'date_passage' => now(),
            'reussi' => $reussi,
            'id_etudiant' => auth()->id(),
            'id_quiz' => $idQuiz
        ]);

        $feedback = $reussi ? "Bravo ! Vous avez réussi le quiz avec {$scorePourcentage}%" 
                            : "Vous avez obtenu {$scorePourcentage}%. La note de passage est de {$quiz->note_passage}%. Essayez à nouveau.";

        return response()->json([
            'message' => 'Quiz soumis avec succès',
            'score' => round($scorePourcentage, 2),
            'reussi' => $reussi,
            'feedback' => $feedback,
            'resultat_id' => $resultat->id
        ]);
    }

    // Récupérer les quiz d'un cours
    public function getQuizByCours($idCours)
    {
        $quizz = Quiz::where('id_cours', $idCours)->with('questions')->get();
        return response()->json(['quizz' => $quizz]);
    }
}