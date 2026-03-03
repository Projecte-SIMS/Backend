# SIMS – Backend Laravel (Proyecto de Movilidad Sostenible)

**Versión:** Sprint 5 – Primer Despliegue  
**Fecha:** 2026-03-03  
**Última actualización:** 2026-03-03

---

## Descripción General

El backend del Sistema Inteligente de Movilidad Sostenible (SIMS) es una infraestructura robusta desarrollada con Laravel 12.x y PHP 8.2+. Actúa como el núcleo de procesamiento del ecosistema, gestionando la lógica de negocio, la seguridad perimetral mediante tokens API y la persistencia de datos en entornos híbridos (PostgreSQL y MongoDB vía microservicio).

---

## Índice de Documentación

| Documento | Descripción |
|-----------|-------------|
| [Especificaciones del Proyecto](docs/SPECIFICATIONS.md) | Definición de funcionalidades, stack tecnológico y arquitectura |
| [Ecosistema Tecnológico](docs/TECH_STACK.md) | Librerías, componentes y herramientas utilizadas |
| [Referencia de Endpoints](docs/API_ENDPOINTS.md) | Listado completo de rutas de la API |
| [Manual de Despliegue](docs/DEPLOYMENT.md) | Instrucciones de instalación y Docker |
| [Decisiones de Diseño](docs/DECISIONS.md) | Justificación técnica de arquitecturas |
| [Convenciones de Código](docs/CONVENTIONS.md) | Estándares PSR-12 y nomenclatura |
| [Pacto de Contribución](docs/CONTRIBUTING.md) | Código de conducta para colaboradores |

---

## Estructura del Proyecto

```
project-sims-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # AuthController, UserController, RoleController, PermissionController
│   │   │   ├── VehicleController.php
│   │   │   ├── TicketController.php
│   │   │   ├── TicketMessageController.php
│   │   │   ├── ReservationController.php
│   │   │   ├── AdminReservationController.php
│   │   │   ├── IoTController.php
│   │   │   └── ChatbotController.php
│   │   ├── Middleware/
│   │   │   └── EnsureUserIsAdmin.php   # Middleware personalizado admin
│   │   └── Requests/
│   │       ├── StoreTicketRequest.php
│   │       ├── UpdateTicketRequest.php
│   │       └── Vehicle/                # StoreVehicleRequest, UpdateVehicleRequest
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Reservation.php
│   │   ├── Trip.php
│   │   ├── Ticket.php
│   │   ├── TicketMessage.php
│   │   └── CommandLog.php              # Logs de comandos IoT
│   ├── Policies/
│   │   ├── UserPolicy.php
│   │   ├── VehiclePolicy.php
│   │   ├── ReservationPolicy.php
│   │   ├── TicketPolicy.php
│   │   └── RolePolicy.php
│   └── Services/
│       └── VehicleLocationService.php  # Comunicación con microservicio IoT
├── database/
│   ├── factories/                      # UserFactory, VehicleFactory, ReservationFactory, TicketFactory
│   ├── migrations/
│   └── seeders/                        # PermissionsSeeder, RolesSeeder, etc.
├── routes/
│   └── api.php                         # 60+ endpoints REST
├── tests/
│   └── Feature/                        # 8 archivos, 51+ tests
└── docs/                               # Documentación técnica
```

---

## Estado Actual del Backend

### ✅ Completado

| Funcionalidad | Estado |
|---------------|--------|
| API REST completa (60+ endpoints) | ✅ |
| Autenticación con Laravel Sanctum | ✅ |
| Registro público de usuarios | ✅ |
| Sistema RBAC con Spatie Permission | ✅ |
| 3 roles (Admin, Client, Maintenance) | ✅ |
| 15 permisos atómicos | ✅ |
| CRUD completo de Users, Vehicles, Reservations, Tickets | ✅ |
| Control de vehículos IoT (on/off) | ✅ |
| Vinculación dispositivos IoT-Vehículos | ✅ |
| Logs de comandos IoT (CommandLog) | ✅ |
| Chatbot con contexto por rol | ✅ |
| Rate limiting (login, chatbot) | ✅ |
| Middleware admin centralizado | ✅ |
| Tests automatizados (51+ tests) | ✅ |
| Documentación técnica | ✅ |

### ⚠️ Pendiente / Mejoras

| Tarea | Prioridad |
|-------|-----------|
| Laravel Telescope (debugging) | Media |
| Sentry (tracking de errores) | Media |
| OpenAPI/Swagger | Baja |
| Tests de integración adicionales | Baja |

---

## Inicio Rápido (Despliegue Unificado)

Para levantar el backend junto con el resto del ecosistema desde la raíz:

```bash
# 1. Levantar contenedores
docker compose up -d --build

# 2. Instalar dependencias y configurar base de datos
docker exec sims-backend composer install
docker exec sims-backend php artisan key:generate
docker exec sims-backend php artisan migrate:fresh --seed
```

### Configuración de Red Interna
- **Base de Datos:** El host configurado en `.env` debe ser `DB_HOST=db`.
- **IoT Microservice:** Accesible internamente en `http://iot-server:8000`.

---

## Variables de Entorno Críticas

| Variable | Descripción |
|----------|-------------|
| `APP_DEBUG` | `false` en producción |
| `DB_CONNECTION` | `pgsql` |
| `IOT_MICROSERVICE_URL` | URL del microservicio FastAPI (ej: `http://localhost:8001`) |
| `IOT_API_KEY` | API key para autenticar comandos IoT |
| `IOT_TIMEOUT` | Timeout para conexiones IoT (default: 5s) |
| `OPEN_WEBUI_API_KEY` | API key para chatbot IA |
| `OPEN_WEBUI_BASE_URL` | URL base del servicio LLM |
| `OPEN_WEBUI_MODEL` | Modelo LLM a usar |

---

## Tests

```bash
# Ejecutar todos los tests
php artisan test

# Test específico
php artisan test --filter=AuthTest

# Con cobertura
./vendor/bin/phpunit --coverage-html coverage/
```

| Archivo | Tests | Cobertura |
|---------|-------|-----------|
| AuthTest.php | 7 | Login, logout, registro, perfil |
| VehicleTest.php | 8 | CRUD vehículos |
| ReservationTest.php | 8 | Reservas y conflictos |
| TicketTest.php | 9 | Sistema de soporte |
| AdminMiddlewareTest.php | 7 | Protección rutas admin |
| IoTControllerTest.php | 8 | Comandos IoT, health |
| RateLimitingTest.php | 3 | Rate limiting |
| ExampleTest.php | 1 | Test básico |
| **Total** | **51+** | |

---

## Integración con IoT

El backend se comunica con el microservicio IoT (FastAPI) mediante `VehicleLocationService`:

```php
// Obtener ubicaciones de todos los vehículos
$locations = $iotService->getLocations();

// Encender/apagar vehículo
$result = $iotService->turnOn($deviceId);
$result = $iotService->turnOff($deviceId);

// Health check del microservicio
$isOnline = $iotService->healthCheck();

// Vincular dispositivo a vehículo
$result = $iotService->updateDevicePlate($deviceId, $licensePlate);
```

---

**Equipo de Desarrollo SIMS**
