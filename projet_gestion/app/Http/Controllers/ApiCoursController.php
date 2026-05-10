<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiCoursController extends Controller
{
    // Lister tous les cours (public)
    public function index()
    {
        $cours = Cours::with('enseignant')->get();
        return response()->json(['cours' => $cours]);
    }

    // Détails d'un cours (protégé)
    public function show($id)
    {
        $cours = Cours::with('enseignant', 'etudiantsInscrits')->find($id);
        if (!$cours) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }
        return response()->json(['cours' => $cours]);
    }
    public function inscriptions($id)
{
    $cours = Cours::with('etudiantsInscrits')->find($id);
    return response()->json(['etudiants' => $cours->etudiantsInscrits]);
}
    // Créer un cours (protégé - enseignant uniquement)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:200',
            'description' => 'nullable|string',
            'niveau' => 'required|in:debutant,intermediaire,avance',
            'categorie' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        if ($user->role !== 'enseignant' && $user->role !== 'admin') {
            return response()->json(['message' => 'Seul un enseignant peut créer un cours'], 403);
        }

        $cours = Cours::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'niveau' => $request->niveau,
            'categorie' => $request->categorie,
            'id_enseignant' => $user->id,
            'date_creation' => now()->toDateString()
        ]);

        return response()->json([
            'message' => 'Cours créé avec succès',
            'cours' => $cours
        ], 201);
    }

    // S'inscrire à un cours (protégé - étudiant uniquement)
    public function inscrire(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->role !== 'etudiant') {
            return response()->json(['message' => 'Seul un étudiant peut s\'inscrire'], 403);
        }

        $cours = Cours::find($id);
        if (!$cours) {
            return response()->json(['message' => 'Cours non trouvé'], 404);
        }

        $existe = Inscription::where('id_etudiant', $user->id)
            ->where('id_cours', $id)
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Déjà inscrit à ce cours'], 400);
        }

        $inscription = Inscription::create([
            'id_etudiant' => $user->id,
            'id_cours' => $id,
            'date_inscription' => now()->toDateString(),
            'statut' => 'en_cours',
            'progression' => 0
        ]);

        return response()->json([
            'message' => 'Inscription réussie',
            'inscription' => $inscription
        ], 201);
    }
}