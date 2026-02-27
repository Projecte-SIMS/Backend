# Decisiones Técnicas del Proyecto SIMS

Este documento recopila las decisiones técnicas y de diseño tomadas durante el desarrollo del backend del SIMS, justificando la elección de tecnologías y patrones.

## 1. Arquitectura de la Aplicación
* Framework Principal: Se ha elegido Laravel 12.x por ser la versión más reciente que ofrece mejoras significativas en rendimiento, seguridad y experiencia de desarrollo.
* Integración API: El proyecto está enfocado exclusivamente como un backend API RESTful (utilizando Laravel Sanctum) para ser consumido por aplicaciones cliente y sistemas externos de telemetría IoT.

## 2. Estrategia de Persistencia Híbrida
* PostgreSQL: Se utiliza para todos los datos relacionales maestros (usuarios, reservas, roles, permisos y tickets) debido a su soporte nativo para transacciones e integridad referencial.
* MongoDB Atlas: Se ha implementado MongoDB (mediante el driver `mongodb/laravel-mongodb`) para el almacenamiento de datos de geolocalización y telemetría de vehículos. Esta elección responde a la naturaleza no estructurada y el alto volumen de los datos IoT.

## 3. Gestión de Roles y Permisos (RBAC)
* Implementación de RBAC con Spatie: Se ha integrado el paquete `spatie/laravel-permission` para una gestión robusta, escalable y flexible de roles y permisos, permitiendo una separación lógica clara entre usuarios y administradores.
* Laravel Policies: Se utilizan en conjunto con el sistema de Spatie para encapsular la lógica de autorización granular directamente sobre los modelos.

## 4. Autenticación y Seguridad
* Laravel Sanctum: Se ha preferido Sanctum por su ligereza y simplicidad en la implementación de autenticación mediante tokens para APIs, ideal para las necesidades del proyecto.

## 5. Estandarización del Entorno de Ejecución
* Docker: El uso de Docker y Docker Compose es obligatorio para asegurar la paridad de entornos entre desarrollo, pruebas y producción, garantizando el uso de PHP 8.2+ y las versiones correctas de PostgreSQL y MongoDB.

## 6. Integración de Inteligencia Artificial
* Desacoplamiento de IA: El chatbot se integra a través de servicios externos, manteniendo el backend enfocado en la lógica de negocio central de la movilidad sostenible.

## 7. Arquitectura de Soporte (Tickets)
* Modelo de Hilos de Conversación: El sistema de tickets permite una trazabilidad completa del soporte proporcionado al usuario vinculando mensajes cronológicos a cada incidencia abierta.

## 8. Preservación de Datos (Soft Deletes)
* Eliminación Lógica: Se ha implementado el rasgo SoftDeletes en entidades críticas para mantener la integridad histórica de los informes y facilitar la auditoría de incidencias.
