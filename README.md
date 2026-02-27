# SIMS – Backend Laravel (Proyecto de Movilidad Sostenible)

Versión: Sprint 5 – Primer Despliegue
Fecha: 27 de febrero de 2026

## Descripción General

El backend del Sistema Inteligente de Movilidad Sostenible (SIMS) es una infraestructura robusta desarrollada con Laravel 12.x y PHP 8.2+. Actúa como el núcleo de procesamiento del ecosistema, gestionando la lógica de negocio, la seguridad perimetral mediante tokens API y la persistencia de datos en entornos híbridos (PostgreSQL y MongoDB).

## Índice de Documentación

Para consultar información específica, acceda a los siguientes documentos técnicos:

* [Especificaciones del Proyecto](docs/SPECIFICATIONS.md): Definición detallada de funcionalidades, stack tecnológico y arquitectura del sistema.
* [Ecosistema Tecnológico](docs/TECH_STACK.md): Detalle exhaustivo de todas las librerías, componentes y herramientas utilizadas y su función.
* [Referencia de Endpoints](docs/API_ENDPOINTS.md): Listado técnico completo de todas las rutas de la API, métodos permitidos y niveles de acceso.
* [Manual de Despliegue](docs/DEPLOYMENT.md): Instrucciones paso a paso para la instalación, configuración y mantenimiento del entorno mediante Docker.
* [Decisiones de Diseño](docs/DECISIONS.md): Justificación técnica de las arquitecturas, bases de datos y patrones de diseño seleccionados.
* [Convenciones de Código](docs/CONVENTIONS.md): Estándares de programación (PSR-12), nomenclatura de archivos y flujo de trabajo en Git.
* [Pacto de Contribución](docs/CONTRIBUTING.md): Código de conducta y estándares de comportamiento para colaboradores.
* [Licencia](LICENCE.md): Texto legal de la Licencia Pública de la Unión Europea (EUPL v1.2).

## Inicio Rápido (Despliegue con Docker)

Si ya dispone de Docker y Docker Compose, puede iniciar el sistema ejecutando:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Para una configuración avanzada o resolución de problemas, consulte el [Manual de Despliegue](docs/DEPLOYMENT.md).

---
Equipo de Desarrollo SIMS
