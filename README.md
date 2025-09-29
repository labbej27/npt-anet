# Numérique pour Tous – Anet (Laravel)

Projet libre (Laravel + Filament) pour une association qui organise des **ateliers d’inclusion numérique** (logiciels libres) avec **réservation en ligne**.

* **Ateliers** : mercredis après-midi à la **Mairie d’Anet**
* **Créneaux** : 3 × 1h (14:00–15:00, 15:00–16:00, 16:00–17:00)
* **Capacité** : 5 personnes / créneau
* **Admin** : Filament (CRUD sessions, réservations, articles, pages)
* **Réservations** : e‑mail + fichier **ICS** (invitation calendrier)
* **2FA** : authentification à double facteur (Google Authenticator) pour l’admin
* **Calendrier public** : FullCalendar (mois/semaine/jour, clic → réservation)

---

## Sommaire

* [Stack & prérequis](#stack--prérequis)
* [Installation](#installation)
* [Configuration](#configuration)
* [Base de données & seeders](#base-de-données--seeders)
* [Admin & 2FA](#admin--2fa)
* [Fonctionnalités](#fonctionnalités)
* [Scripts utiles](#scripts-utiles)
* [Mise en production](#mise-en-production)
* [Publier ce projet sur GitHub](#publier-ce-projet-sur-github)
* [Licence](#licence)

---

## Stack & prérequis

* **PHP** ≥ 8.2, **Composer**
* **Node.js** ≥ 20.19 (ou 22.12+), **npm**
* **Base de données** : MySQL/MariaDB ou PostgreSQL
* **Laravel** 12
* **Filament** 3 (admin) + Breezy (profil, 2FA)
* **Spatie Media Library** (images des articles, WebP + conversions)
* **FullCalendar** 6 (calendrier public)

> Conseil : utilisez **nvm** pour caler la version de Node.

```bash
nvm install 22
nvm use 22
```

---

## Installation

```bash
# 1) Récupérer le projet
git clone <votre-fork-ou-repo> anet-numerique
cd anet-numerique

# 2) Dépendances PHP
composer install

# 3) Environnement
cp .env.example .env
php artisan key:generate
# ➜ éditez .env pour DB_*, MAIL_*, APP_* (voir section Configuration)

# 4) Dépendances front
npm ci
npm run build   # ou npm run dev en local

# 5) Stockage public (médias)
php artisan storage:link

# 6) Migrations & seeders
php artisan migrate
php artisan db:seed --class=WorkshopSeeder    # génère 12 semaines de mercredis × 3 créneaux
php artisan db:seed --class=PageSeeder        # crée les pages: contact, mentions légales, intro asso

# 7) Créer un administrateur Filament
php artisan make:filament-user
# ➜ suivez l’assistant (email / mot de passe). L’URL admin est /admin

# 8) Lancer le serveur
php artisan serve
```

---

## Configuration

Dans **.env** :

```env
APP_NAME="Numérique pour Tous – Anet"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_TIMEZONE=Europe/Paris

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anet
DB_USERNAME=anet
DB_PASSWORD=secret

# E‑mail (exemple Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=contact@numerique-anet.fr
MAIL_FROM_NAME="Numérique pour Tous – Anet"
```

---

## Base de données & seeders

* **`WorkshopSeeder`** : crée automatiquement 12 semaines de créneaux le mercredi (14h/15h/16h).
* **`PageSeeder`** : crée les pages **Contact**, **Mentions légales** et un bloc **Présentation** (slug `association-intro`).

Exécuter :

```bash
php artisan migrate
php artisan db:seed --class=WorkshopSeeder
php artisan db:seed --class=PageSeeder
```

> Vous pouvez éditer ces contenus dans l’admin Filament : **Contenus → Pages**.

---

## Admin & 2FA

* Accès : `http://votre-domaine/admin`
* **Création d’un admin** : `php artisan make:filament-user`
* **2FA (Google Authenticator)** :

  1. Connectez‑vous à l’admin
  2. Ouvrez **Mon profil** (menu utilisateur)
  3. Activez **Two-factor Authentication**
  4. Scannez le **QR Code** dans Google Authenticator (ou Authy), entrez le code à 6 chiffres

> La 2FA est gérée via **Filament Breezy**. Si vous forcez la 2FA, pensez à garder un **code de récupération**.

---

## Fonctionnalités

* **Public**

  * Page Accueil / Association
  * **Agenda** (liste par semaine/jour)
  * **Calendrier** (FullCalendar) : vue mois/semaine/jour, clic → modale de réservation
  * **Réservation** : formulaire (nom, e‑mail, téléphone) → e‑mail de confirmation + **.ics**
  * Lien d’**annulation** autonome
  * **Articles** (couverture + galerie, carrousel avec zoom plein écran)

* **Admin (Filament)**

  * **Ateliers / Créneaux** (WorkshopSession)
  * **Réservations** (liste, annulation)
  * **Articles** (titre, contenu, images WebP, galerie)
  * **Pages** (Contact, Mentions légales, Présentation)
  * **Profil** (mot de passe, 2FA)

---

## Scripts utiles

```bash
# Dev – serveur Laravel + Vite
composer run dev

# Nettoyage caches
php artisan optimize:clear

# Regénérer les conversions médias (vignettes, webp)
php artisan media-library:regenerate
```

---

## Mise en production

```bash
php artisan migrate --force
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

* Configurez un **worker de queue** si vous envoyez les e‑mails en asynchrone
* HTTPS + en‑têtes de sécurité (CSP), logs rotatifs, backups DB

---

## 🧪 Commandes dev

```bash
php artisan serve                     # serveur dev
npm run dev                           # Vite en HMR
npm run build                         # build de prod
php artisan migrate:fresh --seed      # reset DB + seeders
php artisan optimize                  # optimisations caches
```

---

## ☁️ Déploiement OVH (mutualisé)

Deux scénarios : **avec SSH** (recommandé) ou **sans SSH** (FTP seulement).

### A) Avec SSH

1. **Cible web** = dossier `public/` : dans **OVH Manager > Hébergements > Multisite**, réglez le **dossier racine** sur `/www/public` (ou adaptez si vous uploadez ailleurs)
2. Poussez le code (via Git/rsync) sur le serveur
3. Sur le serveur :

   ```bash
   composer install --no-dev -o
   npm ci && npm run build     # ou build local puis upload du dossier public/build
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

### B) Sans SSH (FTP uniquement)

**Principe :** faire les installations **en local**, puis **uploader**. Ensuite, déclencher `storage:link` via un **petit script protégé**.

1. **En local** :

   ```bash
   composer install --no-dev -o
   npm ci && npm run build
   php artisan migrate --force   # ou export SQL et import via phpMyAdmin OVH
   ```

   * Vérifiez que `public/build/` (Vite) est présent
   * Mettez votre **.env** de prod (avec APP_KEY) prêt

2. **Upload FTP** : uploadez **tout** (sauf `.git`, `node_modules`, `tests`, etc.). Assurez-vous que la **racine du site** pointe sur le **sous-dossier `public/`**.

3. **Créer le lien storage** sans SSH :

   * Créez `public/link-storage.php` avec le contenu ci-dessous
   * Ajoutez un **token** dans `.env` : `STORAGE_LINK_TOKEN=quelquechose_de_long`
   * Ouvrez **[https://votre-domaine.tld/link-storage.php?token=quelquechose_de_long](https://votre-domaine.tld/link-storage.php?token=quelquechose_de_long)** une fois, vous devez lire `ok`. **Supprimez** le fichier ensuite.

```php
<?php
// public/link-storage.php — à créer puis SUPPRIMER après usage
$token = $_GET['token'] ?? '';
if (!$token || $token !== getenv('STORAGE_LINK_TOKEN')) { http_response_code(403); exit('forbidden'); }

// 1) Tente la commande artisan officielle
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('storage:link');
    echo 'ok (artisan)';
    exit;
} catch (Throwable $e) {
    // 2) Fallback: symlink direct (si autorisé par l’hébergeur)
    $target = realpath(__DIR__ . '/../storage/app/public');
    $link   = __DIR__ . '/storage';
    if ($target && !is_link($link)) {
        @symlink($target, $link);
    }
    if (is_link($link)) {
        echo 'ok (symlink)';
        exit;
    }
    http_response_code(500);
    echo 'failed: ' . $e->getMessage();
}
```

> Si OVH bloque `symlink()`, gardez la solution **artisan** (qui crée un lien symbolique également) — sur la plupart des offres mutualisées récentes, ça fonctionne. En dernier recours, vous pouvez exposer les fichiers via un **filesystem public** ou une **route dédiée**, mais c’est moins optimal.

4. **Optimisation** (optionnel, via petit script one‑shot si pas de SSH) :

   * `public/optimize.php` :

```php
<?php
$token = $_GET['token'] ?? '';
if (!$token || $token !== getenv('STORAGE_LINK_TOKEN')) { http_response_code(403); exit('forbidden'); }
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');
echo 'ok';
```

* Exécutez **une fois** l’URL `https://votre-domaine.tld/optimize.php?token=...`, puis **supprimez** le fichier.

---

## 🔐 Sécurité (rappels)

* Ne laissez **jamais** `link-storage.php` / `optimize.php` en ligne après usage
* `APP_DEBUG=false` en prod
* Activez la **2FA** pour tous les comptes admin
* Configurez SPF/DKIM/DMARC pour les e‑mails

---

## Licence

**MIT** — libre d’usage et de modification. Merci de citer l’association **Numérique pour Tous – Anet** si vous réutilisez ce projet.
