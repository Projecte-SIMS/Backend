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

## Endpoints Reales (actualizado 2026-02-25)

| Ruta | Método | Descripción | Acceso |
|------|--------|-------------|--------|
| /api/login | POST | Login de usuario | Público |
| /api/logout | POST | Cierre de sesión | Autenticado |
| /api/chatbot/chat | POST | Chat con AI Assistant | Autenticado |
| /api/users/me | GET | Perfil usuario autenticado | Autenticado |
| /api/users/me | PUT | Editar perfil usuario | Autenticado |
| /api/users/me | DELETE | Eliminar usuario | Autenticado |
| /api/vehicles | GET | Listar vehículos | Autenticado |
| /api/vehicles/map | GET | Mapa de vehículos | Autenticado |
| /api/vehicles/{vehicle} | GET | Ver detalle vehículo | Autenticado |
| /api/tickets | GET | Listar tickets usuario | Autenticado |
| /api/tickets | POST | Crear ticket | Autenticado |
| /api/tickets/{ticket} | GET | Ver ticket | Autenticado |
| /api/tickets/{ticket}/messages | POST | Añadir mensaje a ticket | Autenticado |
| /api/reservations | GET | Listar reservas usuario | Autenticado |
| /api/reservations | POST | Crear reserva | Autenticado |
| /api/reservations/{reservation} | GET | Ver reserva | Autenticado |
| /api/reservations/{reservation}/activate | POST | Activar reserva | Autenticado |
| /api/reservations/{reservation}/cancel | POST | Cancelar reserva | Autenticado |
| /api/reservations/{reservation}/finish | POST | Finalizar reserva | Autenticado |
| /api/reservations/{reservation}/force-finish | POST | Forzar fin reserva | Autenticado |
| /api/messages/{message} | DELETE | Eliminar mensaje ticket | Autenticado |
| /api/admin/users | GET | Listar usuarios | Admin |
| /api/admin/users | POST | Crear usuario | Admin |
| /api/admin/users/{user} | GET | Ver usuario | Admin |
| /api/admin/users/{user} | PUT/PATCH | Editar usuario | Admin |
| /api/admin/users/{user} | DELETE | Eliminar usuario | Admin |
| /api/admin/vehicles | GET | Listar vehículos | Admin |
| /api/admin/vehicles | POST | Crear vehículo | Admin |
| /api/admin/vehicles/{vehicle} | GET | Ver vehículo | Admin |
| /api/admin/vehicles/{vehicle} | PUT/PATCH | Editar vehículo | Admin |
| /api/admin/vehicles/{vehicle} | DELETE | Eliminar vehículo | Admin |
| /api/admin/vehicles/map | GET | Mapa vehículos admin | Admin |
| /api/admin/tickets | GET | Listar tickets | Admin |
| /api/admin/tickets | POST | Crear ticket | Admin |
| /api/admin/tickets/{id} | GET | Ver ticket | Admin |
| /api/admin/tickets/{id} | PUT | Editar ticket | Admin |
| /api/admin/tickets/{id} | DELETE | Eliminar ticket | Admin |
| /api/admin/tickets/{id}/messages | POST | Añadir mensaje a ticket | Admin |
| /api/admin/reservations | GET | Listar reservas | Admin |
| /api/admin/reservations | POST | Crear reserva | Admin |
| /api/admin/reservations/{id} | GET | Ver reserva | Admin |
| /api/admin/reservations/{id} | PUT | Editar reserva | Admin |
| /api/admin/reservations/{id} | DELETE | Eliminar reserva | Admin |
| /api/admin/reservations/{id}/force-finish | POST | Forzar fin reserva | Admin |
| /api/admin/roles | GET | Listar roles | Admin |
| /api/admin/roles | POST | Crear rol | Admin |
| /api/admin/roles/{role} | GET | Ver rol | Admin |
| /api/admin/roles/{role} | PUT/PATCH | Editar rol | Admin |
| /api/admin/roles/{role} | DELETE | Eliminar rol | Admin |
| /api/admin/permissions | GET | Listar permisos | Admin |
| /api/admin/permissions | POST | Crear permiso | Admin |
| /api/admin/permissions/{id} | PUT | Editar permiso | Admin |
| /api/admin/permissions/{id} | DELETE | Eliminar permiso | Admin |


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

## Librerías y dependencias principales

- **Framework:** Laravel 10.x
- **Base de datos:** PostgreSQL (principal), MongoDB Atlas (IoT)
- **Autenticación:** Laravel Sanctum
- **Testing:** PHPUnit
- **Contenedores:** Docker, Docker Compose
- **API externa IA:** Open WebUI (configurable por entorno)
- **Otras:**
  - dpage/pgadmin4 (gestión visual de PostgreSQL)
  - Composer (gestión de dependencias PHP)
  - NunoMaduro/Collision (mejoras debug)
  - brianium/paratest (tests en paralelo)

---

## Recomendaciones

- Añadir tests automatizados.
- Documentar contratos API con Swagger o similar.
- Reforzar validaciones en futuros sprints.

---

**Fin del README – Backend SIMS**
