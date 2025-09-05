#!/usr/bin/env bash
set -Eeuo pipefail

CTX_DIR="context"
OUT="laravel11-upgrade-context.md"   # <- variable simple pour éviter les soucis de copie/collage

SAIL="./vendor/bin/sail"
USE_SAIL=false
if [ -x "$SAIL" ]; then
  USE_SAIL=true
fi

in_php()       { if $USE_SAIL; then "$SAIL" php "$@"; else php "$@"; fi; }
in_composer()  { if $USE_SAIL; then "$SAIL" composer "$@"; else composer "$@"; fi; }
in_artisan()   { if $USE_SAIL; then "$SAIL" artisan "$@"; else php artisan "$@"; fi; }

mkdir -p "$CTX_DIR"
echo "⏳ Collecte (Sail=${USE_SAIL})…"

# 1) Versions & env
(in_php -v || true)                                            > "$CTX_DIR/php-version.txt"
(in_composer -V || true)                                       > "$CTX_DIR/composer-version.txt"
(in_artisan --version || true)                                 > "$CTX_DIR/laravel-version.txt"
(in_artisan about --only=Environment,Drivers,Cache,Queues || true) > "$CTX_DIR/about.txt"

# 2) Dépendances
(in_composer show -D || true)                                  > "$CTX_DIR/dev-deps.txt"
(in_composer show || true)                                     > "$CTX_DIR/all-deps.txt"

# 3) Routes & fichiers clés
(in_artisan route:list --json || true)                         > "$CTX_DIR/routes.json"
(ls -la config || true)                                        > "$CTX_DIR/config-files.txt"
(cat bootstrap/app.php || true)                                > "$CTX_DIR/bootstrap-app.php.txt"

# 4) Runner de tests (phpunit/pest)
if [ -f vendor/bin/phpunit ]; then
  (in_php vendor/bin/phpunit --version || true)                > "$CTX_DIR/phpunit-version.txt"
elif [ -f vendor/bin/pest ]; then
  echo "Pest détecté — exécutez vos tests via: ./vendor/bin/sail test" > "$CTX_DIR/phpunit-version.txt"
else
  echo "Aucun runner de tests détecté (phpunit/pest)"          > "$CTX_DIR/phpunit-version.txt"
fi

# 5) Providers custom
if [ -d app/Providers ]; then
  (ls -la app/Providers || true)                               > "$CTX_DIR/providers.txt"
else
  echo "Pas de dossier app/Providers"                          > "$CTX_DIR/providers.txt"
fi

# 6) Copies utiles
[ -f composer.json ] && cp composer.json "$CTX_DIR/composer.json"
[ -f composer.lock ] && cp composer.lock "$CTX_DIR/composer.lock"

# --- Helpers Markdown ---
safesnip()     { local f="$1"; [ -s "$f" ] && { echo '```'; cat "$f"; echo '```'; } || echo "_(vide ou introuvable)_"; }
safesnip_json(){ local f="$1"; [ -s "$f" ] && { echo '```json'; cat "$f"; echo '```'; } || echo "_(vide ou introuvable)_"; }
safesnip_php() { local f="$1"; [ -s "$f" ] && { echo '```php'; cat "$f"; echo '```'; } || echo "_(vide ou introuvable)_"; }

echo "📝 Génération de $OUT"

cat > "$OUT" <<'EOF'
# Upgrade Laravel 10.x → 11.x — Contexte & Contraintes

## Résumé
- Type d’app : [Monolithe | API]
- Modules clés :
  - Auth : [oui/non, Sanctum/Passport]
  - Queue : [driver=?]
  - Cache : [driver=?]
  - Mailer : [smtp, ses, mailgun, autre]
  - Broadcast : [pusher, redis, autre]
  - Storage : [local, s3, autre]
- Tests : [Unitaires=?, Fonctionnels=?] — Couverture approx : ~X%.

> ⚠️ À compléter manuellement ci-dessus (quelques lignes). Le reste est auto-rempli.

## Décisions & Contraintes
- On **garde la structure Laravel 10.x** (pas de migration vers le slim skeleton 11).
- Changements **minimaux** pour compatibilité 11 (pas de refactor cosmétique).
- Ne pas modifier namespaces/hiérarchie des dossiers.
- Suivre strictement le *Laravel 11 Upgrade Guide*.

## Environnement

### PHP
EOF
safesnip "$CTX_DIR/php-version.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Composer
EOF
safesnip "$CTX_DIR/composer-version.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Laravel (about & version)
**Version :**
EOF
safesnip "$CTX_DIR/laravel-version.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'
**about (Environment, Drivers, Cache, Queues) :**
EOF
safesnip "$CTX_DIR/about.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

## Dépendances

### composer.json (actuel)
EOF
safesnip_json "$CTX_DIR/composer.json" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Dépendances dev (`composer show -D`)
EOF
safesnip "$CTX_DIR/dev-deps.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Dépendances totales (`composer show`)
EOF
safesnip "$CTX_DIR/all-deps.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Packages critiques à vérifier (compléter si utilisé)
- Spatie (permissions, backup, etc.)
- Laravel/UI
- Cashier / Scout / Telescope / Horizon
- Sanctum / Passport
- Autres packages non maintenus : [liste]

## Routes & Middleware

### routes.json (`php artisan route:list --json`)
EOF
safesnip_json "$CTX_DIR/routes.json" >> "$OUT"

cat >> "$OUT" <<'EOF'

### bootstrap/app.php
EOF
safesnip_php "$CTX_DIR/bootstrap-app.php.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Fichiers de configuration présents dans `config/`
EOF
safesnip "$CTX_DIR/config-files.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Providers custom (`app/Providers`)
EOF
safesnip "$CTX_DIR/providers.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

## Tests

### Version du runner
EOF
safesnip "$CTX_DIR/phpunit-version.txt" >> "$OUT"

cat >> "$OUT" <<'EOF'

### Commandes de test
- Avec Sail : `./vendor/bin/sail test` (ou `./vendor/bin/sail php vendor/bin/phpunit`)
- Sans Sail : `php artisan test` ou `vendor/bin/phpunit`
- (optionnel) `php artisan migrate:fresh --seed` en local

## Validation (à compléter)
- Scénarios manuels critiques :
  - [ex: login]
  - [ex: paiement Stripe]
  - [ex: job queue]
  - [ex: event/broadcast temps réel]

## Risques connus (à compléter)
- Middlewares globaux custom
- Providers custom sensibles
- Policies auto-discovery
- Macros Request/Response
- Packages legacy / non maintenus

## Rappels Upgrade
- PHP **≥ 8.2**
- Rester sur la structure 10.x pendant l’upgrade
- Installer explicitement API/Broadcast si requis (post-upgrade)

EOF

echo "✅ Contexte prêt."
echo " - Dossier : $CTX_DIR/"
echo " - Fichier : $OUT"
