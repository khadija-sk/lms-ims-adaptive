# ============================================
# Script de déploiement - LMS Platforme (Windows)
# Membre 4 - Gestion de Projets Informatiques
# ============================================

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "🚀 Déploiement de la plateforme LMS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Vérification PHP
Write-Host "`n📋 Vérification des prérequis..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-Object -First 1
    Write-Host "✅ PHP : $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ PHP non trouvé" -ForegroundColor Red
    exit 1
}

# Installation des dépendances
Write-Host "`n📦 Installation des dépendances..." -ForegroundColor Yellow
php composer.phar install --no-dev --optimize-autoloader

# Configuration .env
Write-Host "`n⚙️ Configuration de l'environnement..." -ForegroundColor Yellow
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "✅ Fichier .env créé" -ForegroundColor Green
} else {
    Write-Host "✅ Fichier .env existant" -ForegroundColor Green
}

# Génération clé
Write-Host "`n🔑 Génération de la clé..." -ForegroundColor Yellow
php artisan key:generate

# Migrations
Write-Host "`n🗄️ Migration de la base de données..." -ForegroundColor Yellow
$migrate = Read-Host "Voulez-vous exécuter les migrations ? (o/n)"
if ($migrate -eq "o") {
    php artisan migrate --force
    Write-Host "✅ Migrations exécutées" -ForegroundColor Green
}

# Seeders
$seed = Read-Host "`nVoulez-vous remplir la base avec des données de test ? (o/n)"
if ($seed -eq "o") {
    php artisan db:seed --force
    Write-Host "✅ Base remplie" -ForegroundColor Green
}

# Optimisations
Write-Host "`n⚡ Optimisations..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "`n=========================================" -ForegroundColor Cyan
Write-Host "✅ Déploiement terminé !" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "`n🌐 Pour démarrer : php artisan serve"
Write-Host "📚 Documentation : http://127.0.0.1:8000/api.html`n"

$start = Read-Host "Démarrer le serveur maintenant ? (o/n)"
if ($start -eq "o") {
    php artisan serve --host=0.0.0.0 --port=8000
}