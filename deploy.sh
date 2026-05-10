#!/bin/bash

# ============================================
# Script de déploiement - LMS Platforme
# Membre 4 - Gestion de Projets Informatiques
# ============================================

echo "========================================="
echo "🚀 Déploiement de la plateforme LMS"
echo "========================================="

# Vérification des prérequis
echo ""
echo "📋 Vérification des prérequis..."

# Vérifier PHP
if command -v php &> /dev/null; then
    echo "✅ PHP installé : $(php -v | head -n 1)"
else
    echo "❌ PHP non trouvé. Installation requise."
    exit 1
fi

# Vérifier Composer
if command -v composer &> /dev/null; then
    echo "✅ Composer installé"
else
    echo "⚠️ Composer non trouvé. Utilisation de composer.phar"
fi

# Vérifier MySQL
if command -v mysql &> /dev/null; then
    echo "✅ MySQL installé"
else
    echo "⚠️ MySQL non trouvé. Assurez-vous que MySQL est démarré."
fi

# Installation des dépendances
echo ""
echo "📦 Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader

# Copier .env si nécessaire
echo ""
echo "⚙️ Configuration de l'environnement..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Fichier .env créé"
else
    echo "✅ Fichier .env existant"
fi

# Générer la clé
echo ""
echo "🔑 Génération de la clé applicative..."
php artisan key:generate

# Lier le stockage
echo ""
echo "🔗 Création du lien de stockage..."
php artisan storage:link

# Exécuter les migrations
echo ""
echo "🗄️ Migration de la base de données..."
read -p "Voulez-vous exécuter les migrations ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo "✅ Migrations exécutées"
else
    echo "⏭️ Migrations ignorées"
fi

# Remplir la base de données
echo ""
read -p "Voulez-vous remplir la base avec des données de test ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    echo "✅ Base de données remplie"
fi

# Optimisations
echo ""
echo "⚡ Optimisations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Cache optimisé"

# Vérification finale
echo ""
echo "========================================="
echo "✅ Déploiement terminé avec succès !"
echo "========================================="
echo ""
echo "🌐 Pour démarrer le serveur :"
echo "   php artisan serve"
echo ""
echo "📚 Documentation API :"
echo "   http://127.0.0.1:8000/api.html"
echo ""

# Démarrer le serveur ?
read -p "Voulez-vous démarrer le serveur maintenant ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🚀 Démarrage du serveur..."
    php artisan serve --host=0.0.0.0 --port=8000
fi