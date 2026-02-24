# Documentación detallada de endpoints admin

## Usuarios (`/api/admin/users`)
- **GET** `/api/admin/users`: Lista todos los usuarios con sus roles. Solo accesible por admin. Devuelve un array de usuarios con sus datos y roles.
- **POST** `/api/admin/users`: Crea un usuario. Espera nombre, username, email, password, activo y role_id. Devuelve el usuario creado.
- **GET** `/api/admin/users/{id}`: Muestra un usuario específico. Devuelve los datos y roles del usuario.
- **PUT** `/api/admin/users/{id}`: Actualiza un usuario. Espera campos similares a creación. Devuelve el usuario actualizado.
- **DELETE** `/api/admin/users/{id}`: Elimina un usuario. Devuelve 204 si éxito.

## Roles (`/api/admin/roles`)
- **GET** `/api/admin/roles`: Lista roles con permisos. Permite filtrar por nombre. Devuelve roles paginados con sus permisos.
- **POST** `/api/admin/roles`: Crea un rol. Espera nombre, descripción, guard_name y permisos. Devuelve el rol creado.
- **GET** `/api/admin/roles/{id}`: Muestra un rol. Devuelve datos y permisos.
- **PUT** `/api/admin/roles/{id}`: Actualiza un rol. Espera campos similares a creación. Devuelve el rol actualizado.
- **DELETE** `/api/admin/roles/{id}`: Elimina un rol. Devuelve 204 si éxito.

## Permisos (`/api/admin/permissions`)
- **GET** `/api/admin/permissions`: Lista todos los permisos agrupados por módulo. Devuelve un objeto con módulos y sus permisos.
- **POST** `/api/admin/permissions`: Crea un permiso. Espera nombre y descripción. Devuelve el permiso creado.
- **PUT** `/api/admin/permissions/{id}`: Actualiza un permiso. Espera nombre y descripción. Devuelve el permiso actualizado.
- **DELETE** `/api/admin/permissions/{id}`: Elimina un permiso. Devuelve 204 si éxito.

## Vehículos (`/api/admin/vehicles`)
- **GET** `/api/admin/vehicles`: Lista vehículos con filtros (matrícula, marca, modelo, activo, búsqueda general). Devuelve vehículos paginados.
- **POST** `/api/admin/vehicles`: Crea un vehículo. Espera datos del vehículo. Devuelve el vehículo creado.
- **GET** `/api/admin/vehicles/{id}`: Muestra un vehículo. Devuelve datos del vehículo.
- **PUT** `/api/admin/vehicles/{id}`: Actualiza un vehículo. Espera datos editables. Devuelve el vehículo actualizado.
- **DELETE** `/api/admin/vehicles/{id}`: Elimina un vehículo. Devuelve 204 si éxito.
- **GET** `/api/admin/vehicles-map`: Devuelve el mapa de vehículos con localización y estado.

## Tickets (`/api/admin/tickets`)
- **GET** `/api/admin/tickets`: Lista todos los tickets. Admin ve todos, usuario solo los suyos. Devuelve tickets con usuario y mensajes.
- **POST** `/api/admin/tickets`: Crea un ticket. Espera vehicle_id, título y descripción. Devuelve el ticket creado.
- **GET** `/api/admin/tickets/{id}`: Muestra un ticket. Devuelve datos, usuario y mensajes.
- **PUT** `/api/admin/tickets/{id}`: Actualiza un ticket. Espera campos editables. Devuelve el ticket actualizado.
- **DELETE** `/api/admin/tickets/{id}`: Elimina un ticket. Devuelve 204 si éxito.
- **POST** `/api/admin/tickets/{id}/messages`: Añade mensaje a un ticket. Espera ticket_id y mensaje. Devuelve el mensaje creado.

## Reservas (`/api/admin/reservations`)
- **GET** `/api/admin/reservations`: Lista reservas con filtros por estado. Devuelve reservas paginadas con usuario, vehículo y viaje.
- **POST** `/api/admin/reservations`: Crea una reserva. Espera datos de reserva. Devuelve la reserva creada.
- **GET** `/api/admin/reservations/{id}`: Muestra una reserva. Devuelve datos, usuario, vehículo y viaje.
- **PUT** `/api/admin/reservations/{id}`: Actualiza una reserva. Espera campos editables. Devuelve la reserva actualizada.
- **DELETE** `/api/admin/reservations/{id}`: Elimina una reserva. Devuelve 204 si éxito.
- **POST** `/api/admin/reservations/{id}/force-finish`: Finaliza una reserva forzadamente. Devuelve la reserva actualizada.

---

Cada endpoint valida permisos y roles. Los endpoints admin requieren rol admin y devuelven errores 403 si no autorizado. Los datos devueltos son en formato JSON, y los errores también.
