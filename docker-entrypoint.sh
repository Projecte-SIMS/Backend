#!/bin/sh
set -e

echo "⚙️ Ejecutando migraciones de la base de datos central..."

# Si FRESH_MIGRATION está seteada, hacemos un reset completo
if [ "$FRESH_MIGRATION" = "true" ]; then
  echo "🔄 Reseteando base de datos (FRESH_MIGRATION=true)..."
  php artisan migrate:fresh --force || true
else
  # De otro modo, ejecutamos las migraciones normalmente
  php artisan migrate --force || true
fi

# Seed initial data if needed (solo en primera ejecución)
php artisan db:seed --force || true

# Arrancar el servidor de Laravel en el puerto que Render nos asigne
# Si no hay puerto (local), usamos el 8000 por defecto
PORT="${PORT:-8000}"
echo "🚀 Arrancando servidor en el puerto $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
