# Especificaciones del Proyecto SIMS

Este documento detalla las especificaciones funcionales y técnicas del backend del Sistema Inteligente de Movilidad Sostenible (SIMS).

## 1. Especificaciones Funcionales

El backend del proyecto SIMS proporciona los servicios necesarios para la gestión centralizada de la movilidad sostenible, el soporte a los usuarios y la administración global del ecosistema bajo un modelo de Software como Servicio (SaaS).

### 1.1. Arquitectura Multi-inquilino (Multitenancy)
* Segregación de Datos: El sistema utiliza una arquitectura de base de datos compartida con esquemas aislados (PostgreSQL Schemas) para garantizar la privacidad y seguridad de la información de cada inquilino.
* Identificación de Inquilino: El sistema identifica al inquilino mediante la cabecera HTTP "X-Tenant" o el parámetro de consulta "tenant" en las peticiones API.
* Gestión de Suscripciones: Control de acceso basado en el estado de facturación y vigencia del contrato del inquilino.

### 1.2. Gestión de Identidad y Acceso (IAM)
* Registro y Autenticación: Sistema robusto de inicio y cierre de sesión basado en tokens API mediante Laravel Sanctum, con alcance limitado al inquilino actual.
* Perfil de Usuario: Endpoints específicos para que los usuarios gestionen su propia información (/api/users/me), permitiendo consulta, actualización y desactivación de la cuenta dentro de su entorno de inquilino.
* Control de Acceso Basado en Roles (RBAC): Implementación avanzada mediante el paquete spatie/laravel-permission, definiendo roles (admin, user) y permisos específicos por inquilino.

### 1.3. Gestión de Flota de Vehículos
* Consulta de Vehículos: Listado de vehículos disponibles con información técnica y de estado, filtrados por la flota asignada al inquilino.
* Representación Geoespacial: Provisión de coordenadas en tiempo real para la visualización de la flota en un mapa interactivo.
* Detalle de Entidad: Información exhaustiva de cada vehículo, incluyendo autonomía, modelo y estado de mantenimiento.

### 1.4. Ciclo de Vida de Reservas
* Creación de Reservas: Los usuarios pueden reservar vehículos disponibles de su respectivo inquilino.
* Gestión de Estados: Implementación de la lógica para activar, cancelar y finalizar el uso del vehículo desde la reserva.
* Finalización Forzada: Herramienta administrativa para gestionar reservas anómalas o fuera de plazo.
* Registro de Viajes: Generación automática de registros históricos (Trips) tras la finalización de cada reserva.

### 1.5. Sistema de Soporte y Tickets
* Gestión de Incidencias: Los usuarios pueden reportar fallos técnicos o dudas operativas.
* Comunicación de Soporte: Sistema de mensajería interna bidireccional entre el usuario que abre el ticket y el equipo de administración del inquilino.
* Seguimiento de Estado: Trazabilidad completa desde la apertura hasta la resolución del ticket.

### 1.6. Administración Global y de Inquilino (Admin API)
* Gestión de Usuarios: CRUD completo para la supervisión y mantenimiento de las cuentas de usuario del inquilino.
* Control de Flota: Administración centralizada del registro, edición y eliminación de vehículos.
* Supervisión de Operaciones: Acceso global a todas las reservas, tickets y logs de actividad dentro del contexto del inquilino.
* Configuración de RBAC: Gestión dinámica de roles y permisos mediante la API de administración.

### 1.7. Asistente Inteligente (AI Chatbot)
* Soporte Automatizado: Integración de un chatbot basado en inteligencia artificial para la resolución inmediata de dudas frecuentes y soporte técnico básico, con capacidad de contextualización por inquilino.

---

## 2. Especificaciones Técnicas

### 2.1. Stack Tecnológico
* Framework: Laravel 12.x operando sobre PHP 8.2+.
* Multitenancy: Stancl Tenancy v3 para la gestión de inquilinos y aislamiento de recursos.
* Persistencia Relacional: PostgreSQL para la gestión de datos maestros, transacciones y configuración de usuarios, utilizando esquemas dinámicos para el aislamiento de inquilinos.
* Persistencia NoSQL: MongoDB Atlas (mediante mongodb/laravel-mongodb) para el almacenamiento de datos de telemetría y geolocalización masiva proveniente de dispositivos IoT.
* Autenticación: Laravel Sanctum para la gestión de tokens.
* Autorización: Spatie Laravel Permission para la gestión de roles y permisos.
* Entorno: Infraestructura dockerizada para asegurar la paridad entre desarrollo y producción.

### 2.2. Arquitectura del Software
* Patrón Arquitectónico: MVC adaptado a API RESTful con soporte multitenant.
* Validación: Uso sistemático de Form Requests para la validación de datos de entrada.
* Autorización: Uso de Laravel Policies y Middleware de Spatie para el control de acceso.
* Capa de Servicios: Delegación de lógica compleja a clases especializadas (ej: VehicleLocationService).
* Pipeline de Inicialización: Middleware especializado para la detección de inquilinos y conmutación de conexiones de base de datos en tiempo de ejecución.

### 2.3. Integraciones y Comunicación
* IoT Gateway (FastAPI): Interfaz externa para la ingesta de telemetría de vehículos.
* AI Service: Comunicación mediante API con motores de lenguaje natural para el servicio de chatbot.

### 2.4. Calidad y Validación Técnica
* Testing Automatizado: Cobertura mediante PHPUnit 11.x, incluyendo pruebas de aislamiento de inquilinos.
* Validación de API: Colecciones de Postman para pruebas de integración.
