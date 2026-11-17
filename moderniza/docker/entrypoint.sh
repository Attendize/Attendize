#!/bin/sh
# Arranque del contenedor "moderniza" en desarrollo.
set -e
cd /app

# 1. Dependencias PHP. Se comprueba vendor/autoload.php (no solo la carpeta vendor):
#    si una instalacion se corta a medias, se repite. En Windows, descomprimir en la
#    carpeta compartida es lento: se amplia el tiempo maximo y se reintenta.
export COMPOSER_PROCESS_TIMEOUT=1200
intento=1
until [ -f vendor/autoload.php ]; do
  if [ "$intento" -gt 4 ]; then
    echo "ERROR: composer install ha fallado 4 veces. Revisa: docker compose logs moderniza"; exit 1
  fi
  echo "Instalando dependencias PHP (intento $intento de 4)..."
  rm -rf vendor/composer/[0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f][0-9a-f] 2>/dev/null || true
  composer install --no-interaction --no-progress || true
  intento=$((intento + 1))
done

# 2. Archivo de entorno y clave de la aplicación
[ -f .env ] || cp .env.example .env
grep -q '^APP_KEY=base64' .env || php artisan key:generate --no-interaction

# 3. Esperar a MySQL. Las migraciones NO se lanzan solas: se ejecutan a mano
#    (docker compose exec moderniza php artisan migrate) para ver qué cambian.
until php artisan db:show >/dev/null 2>&1; do
  echo "Esperando a MySQL (servicio db)..."; sleep 3
done

# 4. Servidor de desarrollo
exec php artisan serve --host=0.0.0.0 --port=8000
