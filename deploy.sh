#!/usr/bin/env bash
#
# Runwrk — deploy on the Hostinger server.
# After `git push` from your PC, open SSH and run:
#
#   cd ~/domains/runwrk.com/runwrk && bash deploy.sh
#
set -e

# Wrapped in main() so a self-overwrite during `git reset --hard` can't run a stale buffer.
main() {
    cd "$(dirname "$0")"

    PHP=/opt/alt/php84/usr/bin/php
    COMPOSER=/usr/local/bin/composer

    echo "--- git ---"
    git fetch origin main
    git reset --hard origin/main

    echo "--- composer ---"
    "$PHP" "$COMPOSER" install --no-dev --optimize-autoloader

    echo "--- database migrations ---"
    "$PHP" artisan migrate --force

    echo "--- storage symlink ---"
    [ -L public/storage ] || "$PHP" artisan storage:link

    echo "--- caches ---"
    "$PHP" artisan optimize:clear
    "$PHP" artisan config:cache
    "$PHP" artisan route:cache
    "$PHP" artisan view:cache

    echo "--- restart queue workers ---"
    "$PHP" artisan queue:restart

    echo
    echo "=== DEPLOY COMPLETE ==="
}

main "$@"
