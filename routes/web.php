<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return '<h1>📚 Documentation API - LMS Platforme</h1>
    <p><strong>Base URL:</strong> <code>http://127.0.0.1:8000/api</code></p>
    
    <h2>🔐 Authentification</h2>
    <p><strong>POST /api/auth/register</strong> - Inscription<br>
    <strong>POST /api/auth/login</strong> - Connexion</p>
    
    <h2>📚 Cours</h2>
    <p><strong>GET /api/cours</strong> - Lister les cours<br>
    <strong>GET /api/cours/{id}</strong> - Détails d\'un cours<br>
    <strong>POST /api/cours</strong> - Créer un cours (enseignant)<br>
    <strong>POST /api/cours/{id}/inscrire</strong> - S\'inscrire (étudiant)</p>
    
    <h2>📝 Quiz</h2>
    <p><strong>GET /api/quiz/{id}</strong> - Récupérer un quiz<br>
    <strong>POST /api/quiz/{id}/soumettre</strong> - Soumettre les réponses</p>
    
    <h2>🎓 Certificats</h2>
    <p><strong>POST /api/certificats</strong> - Générer un certificat<br>
    <strong>GET /api/certificats/verifier/{code}</strong> - Vérifier un certificat</p>
    
    <h2>🤖 IA - Recommandations</h2>
    <p><strong>GET /api/recommend/{id}</strong> - Recommandations de cours</p>
    
    <h2>📊 Analytics</h2>
    <p><strong>GET /api/analytics/etudiant/{id}</strong> - KPIs étudiant<br>
    <strong>GET /api/analytics/cours/{id}</strong> - KPIs cours<br>
    <strong>GET /api/analytics/global</strong> - KPIs global (admin)</p>
    
    <h2>🔑 Header authentification</h2>
    <p><code>Authorization: Bearer {token}</code></p>';
});