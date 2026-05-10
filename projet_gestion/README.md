
# 🎓 Plateforme LMS Adaptative

## 📋 Description
Plateforme de gestion d'apprentissage avec IA, quiz, certificats et analytics.

## 👥 Équipe
- **Membre 4** - Backend Laravel, Qualité, Livraison

## 🛠️ Technologies
- Laravel 13.x
- MySQL / PostgreSQL
- Sanctum (Auth)
- API REST

## 📦 Installation

### Prérequis
- PHP 8.3+
- Composer
- MySQL

### Étapes
```bash
# 1. Cloner le projet
git clone [votre-repo]

# 2. Installer les dépendances
composer install

# 3. Configurer .env
cp .env.example .env
php artisan key:generate

# 4. Base de données
php artisan migrate --seed

# 5. Lancer le serveur
php artisan serve