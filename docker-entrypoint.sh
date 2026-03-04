#!/bin/sh
set -e

# Ejecutar migraciones de la base de datos central (Landlord)
echo "⚙️ Ejecutando migraciones..."
php artisan migrate --force

# Arrancar el servidor de Laravel en el puerto que Render nos asigne
# Si no hay puerto (local), usamos el 8000 por defecto
PORT="${PORT:-8000}"
echo "🚀 Arrancando servidor en el puerto $PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
