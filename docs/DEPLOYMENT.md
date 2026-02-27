# Manual de Despliegue del Proyecto SIMS

Este documento detalla los pasos necesarios para desplegar el backend del Sistema Inteligente de Movilidad Sostenible (SIMS) en un entorno de desarrollo o producción utilizando Docker.

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
  Copia el archivo .env.example y configura las credenciales reales para PostgreSQL, MongoDB Atlas y las claves de API (como OpenAI).
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
* Estructura de Base de Datos:
  Ejecuta las migraciones para crear las tablas necesarias en PostgreSQL:
  ```bash
  docker compose exec app php artisan migrate --force
  ```
* Datos Iniciales (Seeders):
  Opcionalmente, carga los datos maestros y de prueba:
  ```bash
  docker compose exec app php artisan db:seed --force
  ```

## 4. Gestión y Acceso a Servicios
* Backend API: Accesible mediante el puerto configurado (predeterminado 8000).
* PostgreSQL: Puerto 5432 para conexiones externas (si está expuesto en docker-compose.yml).
* pgAdmin4: Entorno gráfico de gestión de base de datos disponible en el puerto 5050.

## 5. Mantenimiento y Actualizaciones del Sistema
* Sincronización de Código:
  ```bash
  git pull origin main
  docker compose restart app
  ```
* Actualización de Esquema:
  ```bash
  docker compose exec app php artisan migrate --force
  ```
* Gestión de Caché:
  Limpieza de configuraciones y datos temporales para aplicar cambios:
  ```bash
  docker compose exec app php artisan config:clear
  docker compose exec app php artisan cache:clear
  ```

## 6. Diagnóstico y Resolución de Problemas
* Revisión de Logs: Monitoriza el estado de los servicios en tiempo real:
  ```bash
  docker compose logs -f
  ```
* Gestión de Permisos: Si se presentan errores de escritura en storage o bootstrap/cache, ejecute:
  ```bash
  docker compose exec app chmod -R 775 storage bootstrap/cache
  docker compose exec app chown -R www-data:www-data storage bootstrap/cache
  ```
