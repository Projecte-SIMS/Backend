# Decisiones de Diseño y Justificación Técnica

Este documento detalla las decisiones arquitectónicas clave tomadas para el backend de SIMS y la lógica técnica tras cada elección.

## 1. Arquitectura Multitenant: Aislamiento por Esquemas de PostgreSQL
En lugar de utilizar bases de datos físicas independientes, se implementó el aislamiento mediante esquemas (Schemas) de PostgreSQL.
- **Justificación**: Esta estrategia ofrece un alto nivel de aislamiento de datos manteniendo la eficiencia de costes en despliegues cloud (como el plan gratuito/starter de Render). Facilita las migraciones masivas y permite compartir un pool de conexiones sin riesgo de filtración de datos entre empresas.

## 2. Autenticación Stateless con Laravel Sanctum
Se seleccionó Laravel Sanctum en lugar de JWT-Auth o Passport para la gestión de sesiones de inquilino.
- **Justificación**: Sanctum es ligero y permite emitir tokens API vinculados estrictamente al esquema de base de datos del inquilino actual. Esto garantiza que un token del Inquilino A sea inválido para el Inquilino B, incluso si el usuario existe en ambos.

## 3. Almacenamiento Híbrido (PostgreSQL + MongoDB)
El sistema utiliza PostgreSQL para datos relacionales de negocio y MongoDB para telemetría IoT de alta frecuencia.
- **Justificación**: PostgreSQL asegura la integridad referencial y transacciones ACID para reservas y facturación. MongoDB Atlas gestiona los datos GPS y sensores no estructurados, permitiendo escrituras masivas y escalabilidad sin penalizar el rendimiento de la base de datos principal.

## 4. Integración IoT Orientada a Servicios
El backend no comunica directamente con el hardware, sino que utiliza un microservicio en FastAPI como intermediario.
- **Justificación**: Esto desacopla la lógica de negocio del protocolo de comunicación de hardware. Permite que el subsistema IoT escale de forma independiente y asegura que las conexiones persistentes (WebSockets) no saturen los procesos de PHP-FPM del backend.

## 5. Control de Acceso Basado en Roles (RBAC) con Spatie
Implementado mediante el paquete laravel-permission de Spatie.
- **Justificación**: Proporciona un estándar de industria para gestionar roles (Admin, Client, Maintenance) y permisos atómicos. Se integra nativamente con los Gates y Policies de Laravel, asegurando una seguridad consistente en toda la API.
