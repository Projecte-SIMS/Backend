# Convenciones de Codificación del Proyecto SIMS

Este documento establece los estándares y convenciones de codificación que se deben seguir para mantener la calidad del código, la coherencia técnica y facilitar el mantenimiento del proyecto.

## 1. Estándares de Estilo de Código PHP
* Estándar PSR-12: Se sigue estrictamente el estándar de codificación PSR-12 (Extended Coding Style) para la consistencia del formato (espacios, llaves y declaraciones).
* Tipado Estricto: Todas las clases y funciones deben declarar tipos para parámetros y retornos. El uso de declare(strict_types=1); se recomienda en nuevos módulos.

## 2. Nomenclatura del Proyecto
* Clases y Modelos: Se utiliza PascalCase (ej: UserController, TicketMessage).
* Métodos y Funciones: Se utiliza camelCase (ej: storeUser, updateVehicle).
* Variables y Propiedades: Se utiliza camelCase (ej: userData, vehicleId).
* Archivos de Migraciones: Nombre descriptivo con marca de tiempo cronológica (ej: 2026_01_19_000040_create_vehicles_table.php).

## 3. Arquitectura y Patrones Laravel
* Validación de Datos: Todas las peticiones entrantes deben validarse mediante FormRequest. La lógica de validación nunca debe residir en el controlador.
* Lógica de Negocio: La lógica compleja debe delegarse a clases de Service (ubicadas en app/Services) o Actions, manteniendo los controladores ligeros (Skinny Controllers).
* Asignación Masiva: Se debe definir explícitamente la propiedad $fillable en todos los modelos para proteger contra ataques de asignación masiva.

## 4. Estándares de Rutas API
* Formato de URL: Las rutas de API deben estar en minúsculas y en plural (ej: /api/vehicles, /api/tickets).
* Acciones de Recurso: Se deben utilizar los nombres de métodos estándar de Laravel (index, show, store, update, destroy) para mantener la consistencia con las rutas de recurso.

## 5. Diseño de Base de Datos
* Integridad Referencial: Las claves foráneas deben definirse siempre en las migraciones utilizando tipos de datos consistentes (unsignedBigInteger).
* Optimización de Consultas: Se deben añadir índices en los campos que se utilicen frecuentemente para búsquedas, filtros o uniones (joins).
* Relaciones Eloquent: Se debe priorizar el uso de relaciones de Eloquent (hasMany, belongsTo, etc.) sobre consultas manuales (Query Builder) para aprovechar la legibilidad y las funciones de carga diferida (eager loading).

## 6. Documentación Interna
* PHPDoc: Las clases y métodos públicos deben contar con bloques de PHPDoc que especifiquen su propósito, parámetros, excepciones y tipos de retorno.
* Código Legible: Se prioriza el código autodocumentado mediante nombres de variables y métodos descriptivos, reduciendo la necesidad de comentarios explicativos innecesarios.

## 7. Gestión de Versiones y Git
* Prefijos de Ramas: Las ramas deben seguir una nomenclatura funcional (ej: feature/, fix/, docs/).
* Mensajes de Commit: Deben ser descriptivos y concisos, empezando por un verbo en infinitivo o imperativo (ej: "Añadir servicio de geolocalización", "Corregir error de autenticación en login").
* Atomicidad: Se deben realizar commits pequeños y atómicos que aborden una única funcionalidad o corrección para facilitar la revisión del código.
