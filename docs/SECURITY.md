# Implementación de Seguridad y Hoja de Ruta

Este documento detalla las medidas de seguridad vigentes en el proyecto SIMS y las mejoras planificadas para futuras fases.

## 1. Medidas de Seguridad Implementadas

### A. Autenticación y Autorización
- **Tokens Vinculados al Inquilino**: Cada token emitido por Sanctum se valida contra el esquema específico del inquilino, impidiendo el acceso cruzado.
- **RBAC (Control de Acceso Basado en Roles)**: Todos los endpoints administrativos están protegidos por el middleware EnsureUserIsAdmin.
- **Firewall de Administración Central**: La gestión global de inquilinos requiere una autenticación separada (CentralAdminAuth).

### B. Aislamiento de Datos
- **Aislamiento de Search Path**: El middleware InitializeTenancyByRequestData asegura que solo el esquema de PostgreSQL del inquilino actual sea accesible durante la petición.
- **Configuración CORS**: Políticas restrictivas en config/cors.php que solo permiten peticiones desde los dominios verificados de Vercel.

### C. Infraestructura y Red
- **Cifrado SSL/TLS**: Toda la comunicación (Frontend, Backend, IoT) se realiza mediante protocolos seguros HTTPS y WSS.
- **Protección de Variables de Entorno**: Las credenciales críticas nunca se almacenan en el código fuente, gestionándose mediante el panel de control de Render/Vercel.

## 2. Hoja de Ruta de Seguridad (Futuro)

### A. Autenticación Avanzada
- **Autenticación de Doble Factor (2FA)**: Implementación de TOTP para cuentas administrativas.
- **Bloqueo por Intentos Fallidos**: Mejora del throttling actual con listas negras de IP ante ataques de fuerza bruta.

### B. Monitorización y Auditoría
- **Alertas en Tiempo Real**: Integración con Sentry para detectar y notificar intentos de acceso no autorizados.
- **Cifrado en Reposo**: Implementación de cifrado para campos sensibles (como datos personales) directamente en la base de datos mediante los servicios de cifrado de Laravel.

### C. Mejoras de Red
- **Red Privada Virtual (VPC)**: Mover la base de datos y el backend a una red privada en Render, exponiendo solo el gateway de la API.
- **Whitelisting de IP**: Restringir el microservicio IoT para aceptar comandos exclusivamente desde las IPs estáticas del backend de producción.
