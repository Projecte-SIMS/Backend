# Referencia de Endpoints de la API SIMS

Este documento proporciona una lista exhaustiva de todos los puntos de entrada (endpoints) disponibles en la API del Sistema Inteligente de Movilidad Sostenible (SIMS), detallando su propósito, método HTTP y nivel de acceso requerido.

**Última actualización:** 2026-03-03

---

## 1. Endpoints Públicos

Estos endpoints no requieren autenticación previa.

| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/login` | AuthController@login | Inicia sesión y devuelve un token de acceso (Sanctum). Rate limit: 5/min. |
| POST | `/api/register` | AuthController@register | Registra un nuevo usuario con rol Client. Rate limit: 5/min. |
| GET | `/api/public/vehicles/map` | VehicleController@publicMap | Mapa público de vehículos disponibles (sin autenticación). |

---

## 2. Endpoints para Usuarios Autenticados (Clientes)

Requieren una cabecera `Authorization: Bearer {token}` válida.

### 2.1. Gestión de Perfil y Sesión
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/logout` | AuthController@logout | Invalida el token actual y cierra la sesión. |
| GET | `/api/users/me` | UserController@me | Obtiene los datos del perfil del usuario autenticado. |
| PUT | `/api/users/me` | UserController@updateMe | Actualiza la información del perfil del usuario autenticado. |
| DELETE | `/api/users/me` | UserController@destroyMe | Elimina de forma lógica (soft delete) la cuenta del usuario. |

### 2.2. Flota de Vehículos
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/vehicles` | VehicleController@index | Lista todos los vehículos disponibles. |
| GET | `/api/vehicles/map` | VehicleController@map | Obtiene coordenadas y estado para visualización en mapa. |
| GET | `/api/vehicles/{vehicle}` | VehicleController@show | Muestra el detalle técnico de un vehículo específico. |

### 2.3. Reservas de Movilidad
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/reservations` | ReservationController@index | Lista las reservas del usuario autenticado. |
| POST | `/api/reservations` | ReservationController@store | Crea una nueva reserva para un vehículo disponible. |
| GET | `/api/reservations/{reservation}` | ReservationController@show | Muestra el detalle de una reserva propia. |
| POST | `/api/reservations/{reservation}/activate` | ReservationController@activate | Activa una reserva para iniciar el viaje. |
| POST | `/api/reservations/{reservation}/finish` | ReservationController@finish | Finaliza el uso del vehículo y cierra la reserva. |
| POST | `/api/reservations/{reservation}/cancel` | ReservationController@cancel | Cancela una reserva antes de ser utilizada. |
| POST | `/api/reservations/{reservation}/on` | ReservationController@turnOn | Enciende el vehículo de una reserva activa. |
| POST | `/api/reservations/{reservation}/off` | ReservationController@turnOff | Apaga el vehículo de una reserva activa. |
| POST | `/api/reservations/{reservation}/force-finish` | ReservationController@forceFinish | Cierre de emergencia de una reserva por parte del usuario. |

### 2.4. Soporte y Tickets
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/tickets` | TicketController@index | Lista los tickets de soporte abiertos por el usuario. |
| POST | `/api/tickets` | TicketController@store | Crea una nueva solicitud de soporte o incidencia. |
| GET | `/api/tickets/{ticket}` | TicketController@show | Muestra el hilo de conversación de un ticket específico. |
| POST | `/api/tickets/{ticket}/messages` | TicketMessageController@store | Envía un mensaje dentro de un ticket existente. |
| DELETE | `/api/messages/{message}` | TicketMessageController@destroy | Elimina un mensaje propio enviado en un ticket. |

### 2.5. Asistente IA
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| POST | `/api/chatbot/chat` | ChatbotController@chat | Envía consulta al asistente IA. Rate limit: 20/min. |

### 2.6. Dispositivos IoT (Lectura)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/iot/devices` | IoTController@devices | Lista todos los dispositivos IoT conectados. |
| GET | `/api/iot/devices/{deviceId}` | IoTController@device | Detalle de un dispositivo específico. |
| GET | `/api/iot/devices/{deviceId}/ping` | IoTController@ping | Verifica si un dispositivo está online. |

---

## 3. Endpoints de Administración (Admin API)

Requieren autenticación y pertenecer al rol de administrador. Se encuentran bajo el prefijo `/api/admin`.

### 3.1. Gestión de Usuarios
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/users` | UserController@index | Listado global de todos los usuarios registrados. |
| POST | `/api/admin/users` | UserController@store | Crea un nuevo usuario manualmente. |
| GET | `/api/admin/users/{user}` | UserController@show | Detalle completo de cualquier usuario del sistema. |
| PUT/PATCH | `/api/admin/users/{user}` | UserController@update | Modifica los datos de un usuario. |
| DELETE | `/api/admin/users/{user}` | UserController@destroy | Eliminación administrativa de una cuenta. |

### 3.2. Gestión de la Flota (Admin)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/vehicles` | VehicleController@index | Listado administrativo de la flota. |
| POST | `/api/admin/vehicles` | VehicleController@store | Registra un nuevo vehículo en la plataforma. |
| GET | `/api/admin/vehicles/map` | VehicleController@adminMap | Vista del mapa con información técnica extendida. |
| GET | `/api/admin/vehicles/{vehicle}` | VehicleController@show | Detalle administrativo del vehículo. |
| PUT/PATCH | `/api/admin/vehicles/{vehicle}` | VehicleController@update | Actualiza especificaciones o estado de un vehículo. |
| DELETE | `/api/admin/vehicles/{vehicle}` | VehicleController@destroy | Elimina un vehículo de la flota. |

### 3.3. Control de Reservas (Admin)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/reservations` | AdminReservationController@index | Supervisión de todas las reservas del sistema. |
| POST | `/api/admin/reservations` | AdminReservationController@store | Crea una reserva de forma administrativa. |
| GET | `/api/admin/reservations/{id}` | AdminReservationController@show | Consulta técnica de una reserva ajena. |
| PUT | `/api/admin/reservations/{id}` | AdminReservationController@update | Modifica los parámetros de una reserva. |
| DELETE | `/api/admin/reservations/{id}` | AdminReservationController@destroy | Elimina el registro de una reserva. |
| POST | `/api/admin/reservations/{id}/force-finish` | AdminReservationController@forceFinish | Cierre administrativo forzado de una reserva activa. |

### 3.4. Gestión de Tickets (Admin)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/tickets` | TicketController@index | Bandeja de entrada de todos los tickets del sistema. |
| POST | `/api/admin/tickets` | TicketController@store | Crea un ticket de forma administrativa. |
| GET | `/api/admin/tickets/{ticket}` | TicketController@show | Ver detalle de un ticket. |
| PUT | `/api/admin/tickets/{ticket}` | TicketController@update | Modifica el estado o prioridad de un ticket. |
| DELETE | `/api/admin/tickets/{ticket}` | TicketController@destroy | Cierra o elimina un ticket de forma administrativa. |
| POST | `/api/admin/tickets/{ticket}/messages` | TicketMessageController@store | Responde a un usuario dentro de un ticket de soporte. |

### 3.5. Configuración de Seguridad (RBAC)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/roles` | RoleController@index | Listado de roles disponibles. |
| POST | `/api/admin/roles` | RoleController@store | Crea un nuevo rol en el sistema. |
| GET | `/api/admin/roles/{role}` | RoleController@show | Detalle de un rol y sus permisos. |
| PUT/PATCH | `/api/admin/roles/{role}` | RoleController@update | Modifica la configuración de un rol. |
| DELETE | `/api/admin/roles/{role}` | RoleController@destroy | Elimina un rol del sistema. |
| GET | `/api/admin/permissions` | PermissionController@index | Listado de todos los permisos atómicos definidos. |
| POST | `/api/admin/permissions` | PermissionController@store | Registra un nuevo permiso. |
| PUT | `/api/admin/permissions/{id}` | PermissionController@update | Actualiza la definición de un permiso. |
| DELETE | `/api/admin/permissions/{id}` | PermissionController@destroy | Elimina un permiso del sistema. |

### 3.6. Control IoT (Admin)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| GET | `/api/admin/iot/health` | IoTController@health | Verifica el estado del microservicio IoT. |
| GET | `/api/admin/iot/logs` | IoTController@logs | Lista logs de comandos IoT enviados. |
| POST | `/api/admin/iot/devices/{deviceId}/on` | IoTController@turnOn | Enciende un vehículo remotamente. |
| POST | `/api/admin/iot/devices/{deviceId}/off` | IoTController@turnOff | Apaga un vehículo remotamente. |
| POST | `/api/admin/iot/devices/{deviceId}/command` | IoTController@sendCommand | Envía comando genérico (on/off/reboot). |
| POST | `/api/admin/iot/devices/{deviceId}/link` | IoTController@linkToVehicle | Vincula dispositivo IoT a un vehículo. |
| GET | `/api/admin/iot/devices/unlinked` | IoTController@unlinkedDevices | Lista dispositivos sin vincular (AUTO-*). |
| GET | `/api/admin/iot/vehicles/available` | IoTController@availableVehicles | Lista vehículos disponibles para vincular. |

---

## 4. Rate Limiting

| Grupo | Límite | Aplicado a |
|-------|--------|------------|
| `login` | 5 peticiones/minuto por IP | `/api/login`, `/api/register` |
| `chatbot` | 20 peticiones/minuto por usuario | `/api/chatbot/chat` |
| `api` | 60 peticiones/minuto por usuario | Resto de endpoints autenticados |

---

## 5. Códigos de Respuesta HTTP

| Código | Significado |
|--------|-------------|
| 200 | Operación exitosa |
| 201 | Recurso creado |
| 204 | Eliminación exitosa |
| 400 | Error de validación de negocio |
| 401 | No autenticado |
| 403 | No autorizado / Usuario inactivo |
| 404 | Recurso no encontrado |
| 409 | Conflicto (ej: vehículo ya reservado) |
| 422 | Error de validación de datos |
| 429 | Rate limit excedido |
| 500 | Error interno del servidor |
| 503 | Servicio no disponible (IoT offline) |
