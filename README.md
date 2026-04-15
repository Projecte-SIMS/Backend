# SIMS Backend - Core Tecnológico Multitenant SaaS

Este repositorio contiene el núcleo lógico y la infraestructura de datos del Sistema Inteligente de Movilidad Sostenible (SIMS). Desarrollado con Laravel 12 y PHP 8.2+, el sistema implementa una arquitectura Multitenant avanzada mediante aislamiento físico por esquemas de PostgreSQL.

---

## Estructura Técnica de Directorios

La organización del código sigue los estándares de Laravel, con extensiones específicas para el soporte multi-inquilino y la integración IoT.

```
project-sims-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # Controladores Centrales (Auth, Tenant, Billing)
│   │   │   ├── VehicleController.php   # Lógica de flota local
│   │   │   ├── ReservationController.php # Flujo de reservas y viajes
│   │   │   ├── IoTController.php       # Gestión de hardware y comandos remotos
│   │   │   └── ChatbotController.php   # Integración con servicios de IA
│   │   ├── Middleware/
│   │   │   ├── CentralAdminAuth.php    # Validación de SuperAdministradores
│   │   │   ├── TenantSanctumAuth.php   # Autenticación basada en esquemas locales
│   │   │   ├── EnsureTenantBillingIsActive.php # Control de acceso por facturación
│   │   │   └── InitializeTenancyByRequestData.php # Inyección de contexto de inquilino
│   ├── Models/
│   │   ├── Tenant.php                  # Modelo Central (Esquema public)
│   │   ├── User.php                    # Modelo de Usuario (Esquema local)
│   │   ├── Vehicle.php                 # Entidad de vehículo vinculada a IoT
│   │   └── Reservation.php             # Gestión de estados de alquiler
│   └── Services/
│       ├── Billing/                    # Abstracción de pasarela Stripe
│       └── VehicleLocationService.php  # Integración con microservicio FastAPI
├── config/
│   ├── tenancy.php                     # Reglas de identificación y aislamiento
│   └── sanctum.php                     # Configuración de tokens multi-esquema
├── database/
│   ├── migrations/                     # Tablas del Landlord (Central)
│   └── migrations/tenant/              # Tablas replicadas para cada Inquilino
└── routes/
    └── api.php                         # Definición exhaustiva de endpoints
```

---

## Arquitectura de Aislamiento de Datos

SIMS garantiza la privacidad y seguridad de la información mediante el uso de esquemas de base de datos dinámicos.

### Flujo de Identificación
1.  **Recepción**: La petición llega con la cabecera 'X-Tenant' o el parámetro 'tenant'.
2.  **Activación**: El sistema consulta el Landlord para verificar la existencia del inquilino.
3.  **Conmutación**: Se ejecuta un comando 'SET search_path TO tenant_{id}, public' en PostgreSQL.
4.  **Aislamiento**: Todas las consultas SQL posteriores se ejecutan contra el esquema privado de la organización.

---

## Comandos de Mantenimiento y Operación

El mantenimiento de la infraestructura requiere el uso de comandos específicos de Artisan para asegurar la consistencia en todos los inquilinos.

### Gestión de Base de Datos
*   `php artisan migrate`: Actualiza solo la estructura central (Landlord).
*   `php artisan tenants:migrate`: Ejecuta las migraciones en todos los esquemas de inquilinos existentes.
*   `php artisan tenants:seed`: Población de datos iniciales (roles, permisos) en todos los inquilinos.

### Seguridad
*   `php artisan make:superadmin --email={correo}`: Comando para elevar privilegios a un usuario en el panel central.

---

## Integración con el Ecosistema

### Microservicio IoT
El backend actúa como un orquestador de seguridad. Cuando se solicita la posición de un vehículo, el backend recupera el 'hardware_id' del esquema del inquilino y realiza una petición autenticada al microservicio global de telemetría (FastAPI), asegurando que un inquilino nunca acceda a datos de hardware ajenos.

### Inteligencia Artificial
El sistema integra servicios de LLM para el soporte al usuario, manteniendo contextos aislados por rol y por inquilino para evitar fugas de información entre organizaciones.

---

## Referencias Adicionales

Para más información detallada, consulte los siguientes documentos técnicos en la carpeta docs/:
*   [API_ENDPOINTS.md](docs/API_ENDPOINTS.md): Lista completa de rutas y middlewares.
*   [DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md): Estructura detallada de tablas y columnas.
*   [DEPLOYMENT.md](docs/DEPLOYMENT.md): Guía de configuración para entornos de producción (Render).
*   [TECH_STACK.md](docs/TECH_STACK.md): Stack tecnológico y librerías verificadas.

---
**Ingeniería de Software SIMS - Abril 2026**
