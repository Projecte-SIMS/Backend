# Referencia Técnica de Endpoints de la API SIMS (SaaS Multitenant)

Este documento detalla la totalidad de los puntos de acceso (endpoints) disponibles en la API de SIMS v5.0, organizados por su nivel de acceso y función dentro de la arquitectura multitenant.

---

## 1. Identificación y Aislamiento de Inquilinos

Para todos los endpoints (excepto los de la API Central), se requiere la identificación del inquilino (tenant). Esto es procesado por el middleware `InitializeTenancyByRequestData`.

*   **Header HTTP (Obligatorio):** `X-Tenant: {tenant_id}`
*   **Query Parameter:** `?tenant={tenant_id}`

Si no se proporciona el identificador, el sistema denegará el acceso al esquema de base de datos correspondiente.

---

## 2. API Central (Landlord - Gestión Global)

Estas rutas gestionan la infraestructura de inquilinos y no requieren la cabecera `X-Tenant`. Se protegen con el middleware `CentralAdminAuth`.

| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/central/login` | AuthController@centralLogin | Autenticación de Super Administrador global. |
| POST | `/api/central/billing/webhook/stripe` | BillingController@stripeWebhook | Procesamiento de notificaciones de pago de Stripe. |
| GET | `/api/tenants` | TenantController@index | Listado completo de inquilinos registrados. |
| POST | `/api/tenants` | TenantController@store | Creación de nuevo inquilino (proceso de aprovisionamiento de esquema). |
| GET | `/api/tenants/{id}` | TenantController@show | Consulta de metadatos de un inquilino específico. |
| GET | `/api/tenants/{id}/verify` | TenantController@verify | Verificación de estado del esquema y migraciones del inquilino. |
| POST | `/api/tenants/{id}/domains` | TenantController@addDomain | Asociación de un nuevo dominio técnico al inquilino. |
| DELETE | `/api/tenants/{id}` | TenantController@destroy | Eliminación total y permanente de un inquilino y sus datos. |
| GET | `/api/tenants/{id}/billing/status` | BillingController@status | Consulta del estado de facturación y cuotas del inquilino. |
| POST | `/api/tenants/{id}/billing/checkout-session` | BillingController@checkoutSession | Generación de sesión de pago para el inquilino. |

---

## 3. API de Inquilino (Tenant - Operativa de Negocio)

Requieren la cabecera `X-Tenant`. Los datos se consultan exclusivamente en el esquema del inquilino identificado.

### 3.1. Autenticación y Perfil (Middlewares: api, EnsureTenantBillingIsActive)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/login` | AuthController@login | Inicio de sesión local en el esquema del inquilino. |
| POST | `/api/register` | AuthController@register | Registro de nuevo usuario (rol Client) en el esquema local. |
| POST | `/api/logout` | AuthController@logout | Invalida el token de acceso actual (TenantSanctumAuth). |
| GET | `/api/users/me` | UserController@me | Obtención de datos del perfil del usuario autenticado. |
| PUT | `/api/users/me` | UserController@updateMe | Actualización de datos del perfil del usuario actual. |
| DELETE | `/api/users/me` | UserController@destroyMe | Eliminación lógica de la cuenta del usuario. |

### 3.2. Gestión de Flota y Disponibilidad (Middlewares: TenantSanctumAuth)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/vehicles` | VehicleController@index | Lista de vehículos disponibles en el inventario del inquilino. |
| GET | `/api/vehicles/map` | VehicleController@map | Datos geoespaciales para la renderización de mapas. |
| GET | `/api/vehicles/{vehicle}` | VehicleController@show | Detalle técnico y estado de batería de un vehículo. |
| GET | `/api/public/vehicles/map` | VehicleController@publicMap | Mapa público accesible sin autenticación (pero con X-Tenant). |

### 3.3. Reservas y Telemetría IoT (Middlewares: TenantSanctumAuth)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/reservations` | ReservationController@index | Historial y reservas activas del usuario. |
| POST | `/api/reservations` | ReservationController@store | Creación de una nueva reserva de vehículo. |
| GET | `/api/reservations/{reservation}` | ReservationController@show | Detalle de una reserva específica. |
| POST | `/api/reservations/{reservation}/activate` | ReservationController@activate | Inicio de viaje (cambio de estado y registro de inicio). |
| POST | `/api/reservations/{reservation}/finish` | ReservationController@finish | Finalización de viaje y liberación del vehículo. |
| POST | `/api/reservations/{reservation}/cancel` | ReservationController@cancel | Cancelación de reserva pendiente. |
| POST | `/api/reservations/{reservation}/on` | ReservationController@turnOn | Comando IoT: Activación del motor del vehículo. |
| POST | `/api/reservations/{reservation}/off` | ReservationController@turnOff | Comando IoT: Desactivación del motor del vehículo. |
| POST | `/api/reservations/{reservation}/force-finish` | ReservationController@forceFinish | Cierre de emergencia de reserva. |

### 3.4. Soporte y Mensajería (Middlewares: TenantSanctumAuth)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/tickets` | TicketController@index | Listado de incidencias abiertas por el usuario. |
| POST | `/api/tickets` | TicketController@store | Creación de un nuevo ticket de soporte. |
| GET | `/api/tickets/{ticket}` | TicketController@show | Visualización del hilo de mensajes de un ticket. |
| POST | `/api/tickets/{ticket}/messages` | TicketMessageController@store | Envío de un nuevo mensaje en un ticket. |
| DELETE | `/api/messages/{message}` | TicketMessageController@destroy | Eliminación de un mensaje enviado. |

### 3.5. Asistente IA (Middlewares: Throttle:chatbot)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/chatbot/chat` | ChatbotController@chat | Interacción con el asistente inteligente basado en LLM. |

---

## 4. API de Administración de Inquilino (Tenant Admin)

Rutas bajo el prefijo `/api/admin/`. Requieren autenticación y pertenecer al rol de administrador dentro del inquilino (EnsureUserIsAdmin).

### 4.1. Gestión de Recursos Internos
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/users` | UserController@index | Listado completo de usuarios de la organización. |
| POST | `/api/admin/users` | UserController@store | Alta manual de usuarios por parte del administrador. |
| PUT/PATCH | `/api/admin/users/{user}` | UserController@update | Modificación de datos o roles de un usuario local. |
| GET | `/api/admin/vehicles` | VehicleController@index | Gestión del inventario total de flota. |
| POST | `/api/admin/vehicles` | VehicleController@store | Registro de nuevos vehículos en el esquema. |
| GET | `/api/admin/roles` | RoleController@index | Listado de roles del inquilino. |
| POST | `/api/admin/roles` | RoleController@store | Creación de roles personalizados. |
| GET | `/api/admin/tickets` | TicketController@index | Panel de gestión de soporte al cliente. |

### 4.2. Control Avanzado IoT (Admin IoT)
| Método | Ruta | Controlador@Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/iot/health` | IoTController@health | Estado de conexión con el microservicio global. |
| GET | `/api/admin/iot/devices` | IoTController@devices | Dispositivos IoT actualmente en línea vinculados a este inquilino. |
| POST | `/api/admin/iot/devices/{id}/link` | IoTController@linkToVehicle | Vinculación técnica de hardware IoT con matrícula de vehículo. |
| GET | `/api/admin/iot/logs` | IoTController@logs | Historial detallado de comandos IoT enviados. |

---

## 5. Middleware de Seguridad y Aislamiento

| Middleware | Función |
| :--- | :--- |
| `InitializeTenancyByRequestData` | Extrae el tenant ID y activa el esquema PostgreSQL correspondiente. |
| `CentralAdminAuth` | Valida el acceso de Super Administradores a nivel global (Landlord). |
| `TenantSanctumAuth` | Verifica el token de acceso contra la tabla de tokens del esquema del inquilino. |
| `EnsureTenantBillingIsActive` | Bloquea el acceso si la suscripción del inquilino está suspendida. |
| `EnsureUserIsAdmin` | Restringe el acceso a rutas administrativas dentro del inquilino. |

---
**Documentación de Referencia API SIMS - Abril 2026**
