Aquí ho tens traduït al català:

---

# Estructura actual:

## Usuaris normals:

* **POST /login**: Autenticació d’usuari.
* **POST /logout**: Tancar sessió.
* **GET /user**: Obtenir informació de l’usuari autenticat.
* **POST /users**: Crear usuari.
* **/users (apiResource)**: Gestió d’usuaris (excepte la creació).
* **/roles (apiResource)**: Gestió de rols.
* **GET /permissions**: Llistar permisos.
* **/vehicles (apiResource)**: Gestió de vehicles.
* **GET /vehicles-map**: Veure mapa de vehicles.
* **/tickets (apiResource)**: Gestió de tiquets.
* **POST /tickets/{ticket}/messages**: Afegir missatge a un tiquet.
* **DELETE /messages/{message}**: Eliminar missatge d’un tiquet.
* **GET /reservations**: Llistar reserves de l’usuari.
* **POST /reservations**: Crear reserva.
* **GET /reservations/{reservation}**: Veure una reserva específica.
* **POST /reservations/{reservation}/activate**: Activar reserva.
* **POST /reservations/{reservation}/finish**: Finalitzar reserva.
* **POST /reservations/{reservation}/cancel**: Cancel·lar reserva.
* **POST /reservations/{reservation}/force-finish**: Forçar la finalització de la reserva.

---

## Administradors (prefix /admin):

* **GET /admin/reservations**: Llistar totes les reserves.
* **GET /admin/reservations/{id}**: Veure una reserva específica.
* **PUT /admin/reservations/{id}**: Actualitzar reserva.
* **DELETE /admin/reservations/{id}**: Eliminar reserva.
* **POST /admin/reservations/{id}/force-finish**: Forçar la finalització de la reserva.
* **GET /vehicles-map-admin**: Veure mapa de vehicles per a l’administrador.

---

# Estructura futura d’endpoints d’administració per a cada recurs:

Tots aquests endpoints han d’estar protegits per middleware d’autenticació i autorització d’administrador.

## Usuaris:

* **GET /admin/users**: Llistar tots els usuaris
* **POST /admin/users**: Crear usuari
* **GET /admin/users/{id}**: Veure usuari específic
* **PUT /admin/users/{id}**: Editar usuari
* **DELETE /admin/users/{id}**: Eliminar usuari

## Rols:

* **GET /admin/roles**: Llistar rols
* **POST /admin/roles**: Crear rol
* **GET /admin/roles/{id}**: Veure rol
* **PUT /admin/roles/{id}**: Editar rol
* **DELETE /admin/roles/{id}**: Eliminar rol

## Permisos:

* **GET /admin/permissions**: Llistar permisos
* **POST /admin/permissions**: Crear permís
* **PUT /admin/permissions/{id}**: Editar permís
* **DELETE /admin/permissions/{id}**: Eliminar permís

## Vehicles:

* **GET /admin/vehicles**: Llistar vehicles
* **POST /admin/vehicles**: Crear vehicle
* **GET /admin/vehicles/{id}**: Veure vehicle
* **PUT /admin/vehicles/{id}**: Editar vehicle
* **DELETE /admin/vehicles/{id}**: Eliminar vehicle
* **GET /admin/vehicles-map**: Veure el mapa de tots els vehicles

## Tiquets:

* **GET /admin/tickets**: Llistar tots els tiquets
* **GET /admin/tickets/{id}**: Veure tiquet específic
* **PUT /admin/tickets/{id}**: Editar tiquet
* **DELETE /admin/tickets/{id}**: Eliminar tiquet
* **POST /admin/tickets/{id}/messages**: Afegir missatge a un tiquet

## Reserves:

* **GET /admin/reservations**: Llistar totes les reserves
* **POST /admin/reservations**: Crear reserva
* **GET /admin/reservations/{id}**: Veure reserva
* **PUT /admin/reservations/{id}**: Editar reserva
* **DELETE /admin/reservations/{id}**: Eliminar reserva
* **POST /admin/reservations/{id}/force-finish**: Forçar la finalització

---

# Estructura futura d’endpoints per a usuaris normals per a cada recurs:

Tots aquests endpoints han de mostrar només la informació de l’usuari autenticat i estar protegits per middleware d’autenticació.

## Usuaris:

* **GET /users/me**: Veure informació de l’usuari autenticat
* **PUT /users/me**: Editar la informació del propi usuari
* **DELETE /users/me**: Eliminar el seu propi compte


## Vehicles:

* **GET /vehicles**: Llistar vehicles disponibles
* **GET /vehicles/{id}**: Veure vehicle específic
* **GET /vehicles-map**: Veure mapa de vehicles

## Tiquets:

* **GET /tickets**: Llistar els propis tiquets
* **POST /tickets**: Crear tiquet
* **GET /tickets/{id}**: Veure el propi tiquet
* **POST /tickets/{id}/messages**: Afegir missatge al propi tiquet
* **DELETE /messages/{message}**: Eliminar el propi missatge

## Reserves:

* **GET /reservations**: Llistar les pròpies reserves
* **POST /reservations**: Crear reserva
* **GET /reservations/{id}**: Veure la pròpia reserva
* **POST /reservations/{id}/activate**: Activar la pròpia reserva
* **POST /reservations/{id}/finish**: Finalitzar la pròpia reserva
* **POST /reservations/{id}/cancel**: Cancel·lar la pròpia reserva
* **POST /reservations/{id}/force-finish**: Forçar la finalització de la pròpia reserva

---

Si vols, també t’ho puc adaptar a format més tècnic per documentació (tipus Swagger / OpenAPI) o més formal per memòria de projecte.
