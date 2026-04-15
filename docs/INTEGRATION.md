# Guía de Integración del Sistema Completo SIMS

Este documento detalla la conexión técnica entre todos los subsistemas del proyecto SIMS (SaaS Multitenant).

---

## 1. Integración Backend ↔ Frontend
- **Frontend (Vercel)**: La aplicación Vue 3 se comunica con la API de Laravel (Render) a través de peticiones asíncronas de Axios.
- **Header X-Tenant**: El frontend detecta el identificador de la organización en la URL (ej. ?tenant=feetly) y lo inyecta automáticamente en todas las peticiones para que el backend active el esquema de datos correcto.
- **Autenticación Cruzada**: Se utilizan tokens de Sanctum almacenados en cookies seguras para mantener la sesión del usuario dentro de su propio esquema organizacional.

---

## 2. Integración Backend ↔ Subsistema IoT (FastAPI)
- **Capa Intermedia (Gateway)**: El backend de Laravel actúa como controlador de seguridad, evitando que usuarios no autorizados envíen comandos al hardware.
- **Protocolo de Comandos**: Cuando el administrador envía una orden de "encendido", Laravel envía una petición POST autenticada al microservicio de FastAPI mediante el servicio `VehicleLocationService.php`.
- **Identificador Único (Hardware ID)**: Los vehículos registrados en PostgreSQL tienen un campo `hardware_id` que sirve como puente hacia la telemetría almacenada en MongoDB Atlas.

---

## 3. Documentación del Subsistema IoT

### Requisitos Técnicos
- **Hardware**: Raspberry Pi 4 Model B (4GB RAM) con conectividad a Internet.
- **Software**: Agente IoT en Python 3.11+.
- **Protocolos**: WebSocket para telemetría en tiempo real y HTTP REST para comandos bajo demanda.

### Componentes Hardware Utilizados y Justificación
- **Raspberry Pi 4**: Elegida por su capacidad de procesar tareas de telemetría asíncronas y su compatibilidad nativa con librerías GPIO para el control de relés de vehículos.
- **GPS USB (u-blox)**: Proporciona coordenadas de alta precisión necesarias para el seguimiento de rutas en la plataforma de movilidad.
- **Sensores de Voltaje**: Para monitorizar el estado de la batería del vehículo en tiempo real.

---

## 4. Despliegue y CI/CD (GitHub Actions)
- **Flujo de Despliegue**: El código subido a la rama `main` activa despliegues automáticos en Vercel (Frontend) y Render (Backend / IoT).
- **Control de Integridad**: Durante el despliegue del backend, el sistema ejecuta automáticamente `php artisan tenants:migrate` para asegurar que todas las empresas tengan el esquema actualizado simultáneamente.
