<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiCoursController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\IAController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\AnalyticsController;

Route::get('/recommend/{id}', [RecommendController::class, 'getRecommandations']);
Route::get('/ia/recommandations/{id}', [IAController::class, 'getRecommandations']);
Route::get('/ia/suggestions/{id}', [IAController::class, 'suggestionSimple']);


// Routes publiques
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/cours', [ApiCoursController::class, 'index']);
Route::get('/quiz/{id}', [QuizController::class, 'show']);
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// Route publique pour vérifier un certificat
Route::get('/certificats/verifier/{code}', [CertificatController::class, 'verifier']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/cours/{id}', [ApiCoursController::class, 'show']);
    Route::post('/cours', [ApiCoursController::class, 'store']);
    Route::post('/cours/{id}/inscrire', [ApiCoursController::class, 'inscrire']);
    Route::post('/quiz/{id}/soumettre', [QuizController::class, 'soumettre']);
    Route::get('/cours/{id}/quiz', [QuizController::class, 'getQuizByCours']);
    Route::post('/certificats', [CertificatController::class, 'generer']);
    Route::get('/certificats/{id}/telecharger', [CertificatController::class, 'telecharger']);
    Route::get('/etudiant/{id}/certificats', [CertificatController::class, 'getCertificatsEtudiant']);
     Route::get('/ia/recommandations/{id}', [IAController::class, 'getRecommandations']);
    Route::get('/ia/suggestions/{id}', [IAController::class, 'suggestionSimple']);
    Route::get('/analytics/etudiant/{id}', [AnalyticsController::class, 'kpisEtudiant']);
    Route::get('/analytics/cours/{id}', [AnalyticsController::class, 'kpisCours']);
    Route::get('/analytics/global', [AnalyticsController::class, 'kpisGlobal']);
});