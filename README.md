# Backend Laravel - Système de Gestion Académique IUC

Backend Laravel 11 complet pour le système de gestion académique de l'Institut Universitaire de la Côte (IUC).

## 🚀 Prérequis

- PHP 8.2+
- Composer 2.8+
- MySQL 8.0+
- Node.js (pour les assets si nécessaire)

## 📦 Installation

1. **Cloner le projet** (déjà fait)

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données dans `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=examflowdb
DB_USERNAME=root
DB_PASSWORD=
DB_COLLATION=utf8mb4_unicode_ci
```

5. **Exécuter les migrations**
```bash
php artisan migrate
```

6. **Créer les rôles et permissions**
```bash
php artisan db:seed --class=RolePermissionSeeder
```

7. **Remplir la base de données avec des données de test**
```bash
php artisan db:seed
```

## 🧪 Tests

### Configuration de la base de données de test

La base de données de test est configurée dans `phpunit.xml` :
- Base de données : `examflowdb_test`
- Même structure que la base de données principale

### Exécuter les tests

```bash
# Tous les tests
php artisan test

# Tests feature uniquement
php artisan test --testsuite=Feature

# Test spécifique
php artisan test tests/Feature/AuthTest.php
```

## 📚 Documentation API (Swagger)

La documentation API est disponible via Swagger UI :

1. **Générer la documentation**
```bash
php artisan l5-swagger:generate
```

2. **Accéder à la documentation**
```
http://localhost:8000/api/documentation
```

## 📝 Logs

Les logs de l'API sont stockés dans `storage/logs/api/` avec rotation quotidienne :
- Format : `api-YYYY-MM-DD.log`
- Conservation : 30 jours
- Contenu : Toutes les requêtes/réponses avec utilisateur, IP, temps d'exécution

### Exemple de log

```json
{
  "method": "POST",
  "url": "http://localhost:8000/api/auth/login",
  "path": "auth/login",
  "ip": "127.0.0.1",
  "user_id": null,
  "request_body": {...},
  "timestamp": "2024-12-31T18:00:00Z"
}
```

## 🔐 Comptes de test

Après avoir exécuté les seeders, vous pouvez vous connecter avec :

- **Admin**: `admin@iuc.edu.cm` / `password`
- **Enseignant**: `teacher@iuc.edu.cm` / `password`
- **Étudiant**: `student@iuc.edu.cm` / `password`

## 🌐 API Endpoints

Base URL: `http://localhost:8000/api`

### Authentification
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion
- `POST /api/auth/refresh` - Rafraîchir le token
- `GET /api/auth/me` - Profil utilisateur

### Utilisateurs
- `GET /api/users` - Liste des utilisateurs (pagination)
- `GET /api/users/{id}` - Détails d'un utilisateur
- `POST /api/users` - Créer un utilisateur
- `PUT /api/users/{id}` - Modifier un utilisateur
- `DELETE /api/users/{id}` - Supprimer un utilisateur

### Étudiants
- `GET /api/students` - Liste des étudiants (pagination)
- `GET /api/students/{id}` - Détails d'un étudiant
- `GET /api/students/{id}/grades` - Notes d'un étudiant

### Notes
- `GET /api/grades` - Liste des notes (pagination)
- `POST /api/grades` - Créer une note
- `POST /api/grades/bulk` - Créer des notes en masse
- `POST /api/grades/{id}/validate` - Valider une note
- `POST /api/grades/{id}/reject` - Rejeter une note
- `GET /api/grades/{id}/history` - Historique d'une note

### Statistiques
- `GET /api/statistics/dashboard` - Tableau de bord

## 📄 Pagination

Tous les endpoints de liste supportent la pagination :

```
GET /api/users?page=1&per_page=20
```

Réponse :
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 20,
    "last_page": 5,
    "from": 1,
    "to": 20
  }
}
```

## 🔍 Logging et Audit Trail

Toutes les requêtes API sont automatiquement loggées :
- **Fichier** : `storage/logs/api/api-YYYY-MM-DD.log`
- **Contenu** : Requête (méthode, URL, body, headers), Réponse (status, body, temps d'exécution)
- **Utilisateur** : ID et email de l'utilisateur connecté
- **Audit Trail** : Enregistré dans la table `activity_logs`

## 🐛 Dépannage

Si vous rencontrez des erreurs :

1. Vérifiez que MySQL est démarré
2. Vérifiez les permissions sur `storage/` et `bootstrap/cache/`
3. Exécutez `php artisan config:clear` et `php artisan cache:clear`
4. Vérifiez les logs dans `storage/logs/laravel.log`

## 📄 Licence

Propriétaire - Institut Universitaire de la Côte
