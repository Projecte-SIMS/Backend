# SIMS Backend – API Multitenant (Laravel 12)

Núcleo central del SaaS SIMS. Gestiona la lógica multitenant mediante aislamiento por esquemas de PostgreSQL y la integración con Stripe para facturación.

## 🏗️ Requisitos
- PHP 8.2+ & Composer
- PostgreSQL 16 (con soporte de esquemas)
- MongoDB (para telemetría)
- Redis (para colas y caché)

## 🚀 Instalación y Setup
```bash
# 1. Dependencias
composer install

# 2. Configuración
cp .env.example .env
php artisan key:generate

# 3. Base de Datos Landlord (Central)
php artisan migrate --seed # Crea tablas de tenants, dominios y planes

# 4. Iniciar Servidor
php artisan serve
```

## 🛠️ Comandos Artisan Multitenant
- `php artisan tenants:migrate`: Ejecuta migraciones en todos los inquilinos.
- `php artisan tenants:seed`: Puebla datos en todos los esquemas de inquilinos.
- `php artisan tenants:list`: Muestra todas las organizaciones registradas.

## 📄 Documentación Técnica
- [**Arquitectura de Esquemas**](https://github.com/Projecte-SIMS/.github/blob/main/profile/docs/arquitectura.md)
- [**Estrategia de Facturación (Stripe)**](https://github.com/Projecte-SIMS/.github/blob/main/profile/docs/funcionalidad.md)
- [**Guía de Despliegue**](https://github.com/Projecte-SIMS/.github/blob/main/profile/docs/despliegue.md)
- [**Referencia de API**](./docs/API_ENDPOINTS.md)

## 🔐 Seguridad
El sistema inyecta el contexto de base de datos basándose en la cabecera `X-Tenant`. No es posible acceder a datos de una organización sin el token Sanctum emitido para ese esquema específico.

---
*Para más detalles, consulta el [README principal](../README.md).*
