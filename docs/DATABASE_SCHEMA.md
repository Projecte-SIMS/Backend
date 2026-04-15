# Especificación Detallada del Esquema de Base de Datos

Este documento describe la estructura de datos del sistema SIMS, desglosada por el esquema central (Landlord) y los esquemas dinámicos de inquilinos (Tenants).

---

## 1. Esquema Landlord (Esquema: public)

Gestiona la infraestructura global y la facturación de la plataforma SaaS.

### Tabla: tenants
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | string | No | Identificador único del inquilino (Slug). Usado como nombre del esquema dinámico. |
| data | json | Sí | Metadatos genéricos (Branding, configuración personalizada). |
| billing_provider | string | Sí | Proveedor de pasarela de pago (ej. stripe). |
| billing_status | string | No | Estado comercial (active, trailing, suspended, cancelled). |
| billing_customer_id | string | Sí | Referencia externa del cliente en Stripe. |
| billing_subscription_id | string | Sí | Referencia externa de la suscripción activa. |
| billing_price_id | string | Sí | ID del plan de precios contratado. |
| billing_monthly_amount_cents | integer | Sí | Cuota mensual en céntimos (evita errores de redondeo). |
| created_at | timestamp | No | Fecha de registro inicial. |

### Tabla: domains
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | integer | No | Clave primaria autoincremental. |
| domain | string | No | Nombre de dominio o subdominio vinculado (Unique). |
| tenant_id | string | No | Relación con la tabla tenants (FK). |

---

## 2. Esquema Tenant (Esquema: tenant_{id})

Cada organización posee una réplica de estas tablas dentro de su propio espacio de nombres aislado.

### Tabla: users
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | bigserial | No | Clave primaria. |
| uuid | uuid | No | Identificador único universal para API externa. |
| name | string | No | Nombre completo del usuario. |
| email | string | No | Correo electrónico (Unique dentro del inquilino). |
| username | string | No | Nombre de usuario para login (Unique). |
| password | string | No | Hash de la contraseña. |
| active | boolean | No | Estado de habilitación de la cuenta. |
| deleted_at | timestamp | Sí | Soft delete para recuperación de datos. |

### Tabla: vehicles
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | bigserial | No | Clave primaria. |
| license_plate | string | No | Matrícula única del vehículo. |
| brand | string | No | Marca del fabricante. |
| model | string | No | Modelo del vehículo. |
| status | string | No | Estado operativo (available, busy, maintenance). |
| current_battery | integer | No | Porcentaje de carga (0-100). |
| hardware_id | string | Sí | Identificador del dispositivo IoT vinculado. |

### Tabla: reservations
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | bigserial | No | Clave primaria. |
| user_id | bigint | No | Referencia al usuario que reserva (FK). |
| vehicle_id | bigint | No | Referencia al vehículo reservado (FK). |
| start_date | timestamp | No | Fecha y hora de inicio prevista. |
| end_date | timestamp | No | Fecha y hora de fin prevista. |
| status | string | No | Estado (pending, active, finished, cancelled). |

### Tabla: trips
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | bigserial | No | Clave primaria. |
| reservation_id | bigint | No | Referencia a la reserva origen (FK). |
| user_id | bigint | No | Usuario que realizó el viaje (FK). |
| start_location | json | No | Coordenadas GPS de inicio. |
| end_location | json | Sí | Coordenadas GPS de finalización. |
| distance_km | float | No | Distancia total recorrida. |

### Tabla: command_logs (Auditoría IoT)
| Columna | Tipo | Nulable | Descripción |
| :--- | :--- | :--- | :--- |
| id | bigserial | No | Clave primaria. |
| device_id | string | No | ID del dispositivo receptor. |
| vehicle_plate | string | No | Matrícula del vehículo afectado. |
| user_id | bigint | No | Usuario que envió el comando (FK). |
| command | string | No | Acción ejecutada (on, off, reboot). |
| status | string | No | Resultado del comando (success, failed, timeout). |
| response_payload| json | Sí | Respuesta técnica cruda del hardware. |

---

## 3. Seguridad y Control de Acceso (Spatie RBAC)

Las siguientes tablas gestionan el sistema de permisos basado en roles dentro de cada inquilino:
- **roles**: Define perfiles como 'Admin', 'Client', 'Maintenance'.
- **permissions**: Define capacidades como 'vehicles.create', 'reservations.delete'.
- **model_has_roles**: Vincula usuarios con sus roles respectivos.
- **role_has_permissions**: Vincula roles con sus permisos atómicos.

---
**Especificación Técnica de Datos SIMS - Abril 2026**
