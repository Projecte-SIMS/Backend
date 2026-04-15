# Manual de Despliegue del Proyecto SIMS

Este documento detalla los pasos necesarios para desplegar el backend del Sistema Inteligente de Movilidad Sostenible (SIMS) en un entorno de desarrollo o producción, con especial énfasis en la arquitectura multitenant.

## 1. Requisitos Previos del Sistema
* Docker y Docker Compose: Instalados y funcionando en el sistema operativo anfitrión.
* Git: Para la clonación del repositorio del proyecto.
* Archivo de Entorno: Configuración previa del archivo .env para las credenciales del sistema.

## 2. Configuración Inicial del Entorno
* Clonación del Repositorio:
  ```bash
  git clone [URL_DEL_REPOSITORIO]
  cd project-sims-backend
  ```
* Preparación del Archivo .env:
  Copia el archivo .env.example y configura las credenciales reales para PostgreSQL, MongoDB Atlas y las claves de API. Asegúrate de definir CENTRAL_DOMAIN para la identificación de inquilinos.
  ```bash
  cp .env.example .env
  ```

## 3. Despliegue Mediante Docker
* Construcción de Contenedores:
  Levanta los servicios definidos en docker-compose.yml:
  ```bash
  docker compose up -d --build
  ```
* Gestión de Dependencias (Composer):
  Instala las librerías necesarias de PHP dentro del contenedor:
  ```bash
  docker compose exec app composer install --no-interaction
  ```
* Seguridad de la Aplicación:
  Genera la clave de cifrado única para Laravel:
  ```bash
  docker compose exec app php artisan key:generate --force
  ```

## 4. Gestión de Base de Datos Multitenant
El sistema utiliza una base de datos central para gestionar los inquilinos y esquemas individuales para cada uno de ellos.

* Migraciones Centrales:
  Ejecuta las migraciones para las tablas globales (tenants, domains):
  ```bash
  docker compose exec app php artisan migrate --force
  ```
* Migraciones de Inquilinos (Tenant Schemas):
  Ejecuta las migraciones en todos los esquemas de inquilinos existentes:
  ```bash
  docker compose exec app php artisan tenants:migrate
  ```
* Datos Iniciales (Seeders):
  Carga los datos maestros en la base de datos central:
  ```bash
  docker compose exec app php artisan db:seed --force
  ```
  Para sembrar datos en un inquilino específico:
  ```bash
  docker compose exec app php artisan tenants:seed --tenant=[ID_DEL_TENANT]
  ```

## 5. Configuración en Render
Para el despliegue en Render, se utiliza el archivo render.yaml que automatiza la creación del servicio web y la base de datos PostgreSQL.

* Variables de Entorno Críticas:
  - APP_KEY: Clave de cifrado de la aplicación.
  - DB_CONNECTION: Debe establecerse en "pgsql".
  - DATABASE_URL: Enlace de conexión proporcionado por Render.
  - CENTRAL_DOMAIN: Dominio principal de la API (ej: sims-api.onrender.com).
  - MONGODB_URI: URI de conexión a MongoDB Atlas para telemetría.

* Comandos de Build y Start:
  - Build Command: `composer install --no-interaction --optimize-autoloader && php artisan migrate --force`
  - Start Command: `php artisan serve --host 0.0.0.0 --port $PORT`

## 6. Mantenimiento y Actualizaciones del Sistema
* Sincronización de Código:
  ```bash
  git pull origin main
  docker compose restart app
  ```
* Actualización de Esquemas:
  Es fundamental ejecutar ambos comandos para asegurar la integridad de los datos:
  ```bash
  docker compose exec app php artisan migrate --force
  docker compose exec app php artisan tenants:migrate
  ```
* Gestión de Caché:
  Limpieza de configuraciones y datos temporales:
  ```bash
  docker compose exec app php artisan config:clear
  docker compose exec app php artisan cache:clear
  ```

## 7. Diagnóstico y Resolución de Problemas
* Revisión de Logs: Monitoriza el estado de los servicios en tiempo real:
  ```bash
  docker compose logs -f
  ```
* Verificación de Inquilinos:
  Utiliza Artisan Tinker para verificar la existencia de inquilinos y sus dominios:
  ```bash
  docker compose exec app php artisan tinker
  >>> App\Models\Tenant::all();
  ```
