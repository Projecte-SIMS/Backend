# SIMS – Backend Laravel
**Versión:** Sprint 5 – First Deployment  
**Fecha:** 2026-02-23

---

## Descripción General

Este repositorio contiene el backend del Sistema Intel·ligent de Mobilitat Sostenible (SIMS), desarrollado en Laravel. Gestiona la lógica de negocio, autenticación, autorización, validación y almacenamiento de datos para usuarios, administradores y entidades IoT.

---

## Arquitectura y Componentes

- **Rutas:** Definidas en `routes/web.php` y `routes/api.php` para usuarios, admins y gestión de entidades.
- **Controladores:** Lógica de negocio para vehículos, tickets, reservas, roles, permisos y autenticación.
- **Modelos:** Entidades principales (`User`, `Vehicle`, `Ticket`, `Reservation`, `Role`, `Permission`).
- **Form Requests:** Validación de datos en formularios y APIs.
- **Policies:** Autorización según roles y permisos.
- **Middleware:** Protección de rutas (`auth`, roles).
- **Base de datos:** PostgreSQL y MongoDB Atlas (para datos IoT).

---

## Integración

- **Frontend Blade:** Consume endpoints protegidos para gestión y visualización.
- **IoT FastAPI:** Recibe datos autenticados vía API key y los almacena/gestiona.

---

## Endpoints Principales

| Ruta        | Método | Descripción           | Acceso         |
|-------------|--------|----------------------|----------------|
| /login      | POST   | Login de usuario     | Público        |
| /register   | POST   | Registro de usuario  | Público        |
| /logout     | POST   | Cierre de sesión     | Autenticado    |
| /vehicles   | CRUD   | Gestión de vehículos | Admin          |
| /tickets    | CRUD   | Gestión de tickets   | Usuario/Admin  |
| /reservations | CRUD | Gestión de reservas  | Usuario/Admin  |
| /roles      | CRUD   | Gestión de roles     | Admin          |
| /permissions| CRUD   | Gestión de permisos  | Admin          |

---

## Seguridad y Roles

- **Autenticación:** Middleware `auth` en rutas protegidas.
- **Autorización:** Policies y middleware de roles (`role:user`, `role:admin`).
- **Protección CSRF:** Todos los formularios Blade incluyen `@csrf`.
- **Validaciones:** Form Requests en operaciones críticas.

---

## Despliegue y Entorno

### Variables de entorno
- Copia `.env.example` a `.env` y configura credenciales reales.

### Despliegue con Docker
1. Construir y levantar contenedores:
   ```bash
   docker compose up -d --build
   ```
2. Instalar dependencias:
   ```bash
   docker compose run --rm app composer install --no-interaction
   docker compose exec app composer install --no-interaction
   ```
3. Generar clave de aplicación:
   ```bash
   docker compose exec app php artisan key:generate --force
   ```
4. Ejecutar migraciones:
   ```bash
   docker compose exec app php artisan migrate --force
   ```

### Migraciones
- Para refrescar todas las migraciones y borrar la base de datos:
   ```bash
   docker compose exec app php artisan migrate:fresh --force
   ```

---

## Estado Actual

- Endpoints completos y auditados.
- Seguridad y validación mínima garantizadas.
- Listo para integración y despliegue.

---

## Recomendaciones

- Añadir tests automatizados.
- Documentar contratos API con Swagger o similar.
- Reforzar validaciones en futuros sprints.

---

**Fin del README – Backend SIMS**
