# Ecosistema Tecnológico y Dependencias SIMS

Este documento detalla todos los componentes, librerías, herramientas y servicios que conforman el backend del Sistema Inteligente de Movilidad Sostenible (SIMS), especificando su función crítica dentro del proyecto.

## 1. Núcleo del Sistema (Core)

| Componente | Versión | Función en el Proyecto |
| :--- | :--- | :--- |
| **PHP** | 8.2+ | Lenguaje de programación base para el desarrollo del backend. |
| **Laravel Framework** | 12.x | Framework principal que proporciona la estructura MVC, routing y servicios de sistema. |
| **Composer** | 2.x | Gestor de dependencias de PHP para la administración de librerías de terceros. |

## 2. Gestión de Datos y Persistencia

| Librería / Servicio | Función en el Proyecto |
| :--- | :--- |
| **PostgreSQL 15** | Base de datos relacional principal para usuarios, roles, tickets y reservas. |
| **MongoDB Atlas** | Almacenamiento NoSQL escalable para telemetría y geolocalización de vehículos. |
| **laravel-mongodb** | Driver oficial para integrar Eloquent con MongoDB, permitiendo el uso de modelos sobre colecciones NoSQL. |
| **Redis** | Utilizado opcionalmente para caché y gestión de colas (definido en `config/database.php`). |

## 3. Seguridad y Autorización

| Librería | Función en el Proyecto |
| :--- | :--- |
| **Laravel Sanctum** | Gestión de autenticación mediante tokens de portador (Bearer Tokens) para la API. |
| **Spatie Laravel Permission** | Sistema avanzado de Control de Acceso Basado en Roles (RBAC) y gestión de permisos atómicos. |

## 4. Integraciones y Servicios Externos

| Herramienta / API | Función en el Proyecto |
| :--- | :--- |
| **Open WebUI / OpenAI** | Motor de inteligencia artificial para el procesamiento de lenguaje natural del Chatbot. |
| **Laravel HTTP Client** | Utilizado en `ChatbotController` para realizar peticiones externas seguras al servicio de IA. |
| **FastAPI (Externo)** | Servicio de ingesta de datos IoT que envía telemetría de vehículos a este backend. |

## 5. Desarrollo, Frontend y Assets

Aunque el proyecto es principalmente un backend, cuenta con herramientas para la gestión de interfaces administrativas o assets:

| Herramienta | Función en el Proyecto |
| :--- | :--- |
| **Vite 7.x** | Bundler de nueva generación para la compilación rápida de assets (JS/CSS). |
| **TailwindCSS 4.x** | Framework de CSS utilitario para el diseño rápido y responsivo de componentes visuales. |
| **Axios** | Cliente HTTP para realizar peticiones desde el frontend hacia los endpoints de la API. |
| **Concurrently** | Permite ejecutar varios procesos simultáneamente (servidor PHP + Vite) durante el desarrollo. |

## 6. Calidad y Testing

| Herramienta | Función en el Proyecto |
| :--- | :--- |
| **PHPUnit 11.x** | Framework de pruebas unitarias y de integración para asegurar la robustez del código. |
| **FakerPHP** | Generador de datos falsos para el llenado de bases de datos (Seeders) y pruebas. |
| **Laravel Pint** | Linter de código para asegurar que todo el proyecto sigue los estándares PSR-12 y estilos de Laravel. |
| **Mockery** | Utilizado para la creación de objetos simulados (mocks) en el entorno de pruebas. |
| **Postman** | Colecciones de pruebas manuales y flujos de API ubicadas en `tests/postman`. |

## 7. Infraestructura y Operaciones

| Componente | Función en el Proyecto |
| :--- | :--- |
| **Docker** | Contenedorización de la aplicación para garantizar la paridad de entornos. |
| **Docker Compose** | Orquestación de servicios (App, PostgreSQL, pgAdmin). |
| **pgAdmin 4** | Interfaz web para la administración visual de la base de datos PostgreSQL. |
| **Laravel Sail** | Entorno de desarrollo ligero basado en Docker integrado en Laravel. |
| **Laravel Pail** | Herramienta para la monitorización de logs de la aplicación en tiempo real desde la CLI. |
| **Laravel Tinker** | Entorno REPL para interactuar con la aplicación y la base de datos desde la línea de comandos. |
