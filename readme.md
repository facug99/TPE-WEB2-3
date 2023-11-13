# LocalSoundBA Albums API REST
## Introducción
Bienvenido a la documentación de la API REST de LocalSoundBA para gestionar álbumes de bandas. Esta API permite realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) sobre la información de los álbumes.

La información de los álbumes, presentada en formato JSON, incluye:
- ID (`id`)
- Título (`title`)
- Año de creación (`year`)
- ID de banda (`band_id`)

**Ejemplo de presentación de un álbum:**
```json
{
    "id": 12,
    "title": "Tanto Tango",
    "year": 1998,
    "band_id": 9
}
```

---

## Base URL
La URL base para acceder a la API es: `localhost/web2/TPE-WEB2-3/api/`

---

## Recursos y operaciones CRUD

### 1. Obtener álbumes o bandas
#### 1.1. Obtener listado completo
Los endpoints para obtener el listado de todos los álbumes y bandas son: 
- `GET /albums`
- `GET /bands`

**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums
```

**Response del ejemplo:**
```json
[
    {
        "id": 1,
        "title": "Rock a Medianoche",
        "year": 2000,
        "band_id": 1
    },
    {
        "id": 2,
        "title": "Rock Eterno",
        "year": 1999,
        "band_id": 1
    },
    ...
]
```

##### Parámetros de consulta
###### a. Filtrado
Los resultados de la consulta pueden filtrarse según campos y valores especificados mediante los  parámetros de consulta `filter` y `value`, respectivamente.

Consideraciones: 
- El filtro debe coincidir con algún campo de la tabla de álbumes, de lo contrario se producirá un error 400 (Bad Request).
- Los valores de la consulta representan subcadenas en el filtrado (es decir, se buscarán todos los resultados que *contengan* el valor dado). 
- Los valores de la consulta no son sensibles a mayúsculas y minúsculas (case sensitive).


**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums?filter=title&value=sals
```

**Response del ejemplo:**
```json
[
    {
        "id": 5,
        "title": "Salsa y Pasión",
        "year": 2005,
        "band_id": 3
    }
]
```

###### b. Ordenamiento
Los resultados de la consulta pueden ordenarse según campos y órdenes ("asc" o "desc") especificados mediante los parámetros de consulta `sort` y `order`, respectivamente.

Consideraciones:
- Los valores de la consulta no son sensibles a mayúsculas y minúsculas (case sensitive).
- Los únicos valores admitidos para la clave `order` son "ASC" o "DESC", de lo contrario se producirá un error 400 (Bad Request).

**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums?sort=year&order=desc
```

**Response del ejemplo:**
```json
[
    {
        "id": 11,
        "title": "20 Grandes Éxitos",
        "year": 2020,
        "band_id": 8
    },
    {
        "id": 7,
        "title": "Cachengue Bristol",
        "year": 2016,
        "band_id": 5
    },
    {
        "id": 6,
        "title": "Tropical Heat",
        "year": 2012,
        "band_id": 3
    },
    ...
]
```

###### c. Paginación
Los resultados de la consulta pueden paginarse mediante los parámetros de consulta `page` y `limit`. `page` indicará la página y `limit` la cantidad de resultados por página.

Consideraciones:
- Los valores ingresados tanto para `page` como para `limit` deben ser números enteros positivos, de lo contrario se producirá un error 400 (Bad Request).

**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums?page=2&limit=3
```

**Response del ejemplo:**
```json
[
    {
        "id": 4,
        "title": "Folklore de la Pampa",
        "year": 1999,
        "band_id": 2
    },
    {
        "id": 5,
        "title": "Salsa y Pasión",
        "year": 2005,
        "band_id": 3
    },
    {
        "id": 6,
        "title": "Tropical Heat",
        "year": 2012,
        "band_id": 3
    }
]
```

##### Errores de consulta
Si un parámetro se escribe incorrectamente se produce un error 400 (Bad Request) junto con un mensaje descriptivo en el cuerpo de la respuesta.

**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums?filter=titleee&value=salsa
```

**Response del ejemplo:**
```json
"Invalid filter parameter (field 'titleee' does not exist)"
```

#### 1.2. Obtener por ID
Los endpoints para obtener un álbum o banda con un ID específico son:
- `GET /albums/:id`
- `GET /bands/:id`

**Ejemplo de request:**
```url
GET localhost/web2/TPE-WEB2-3/api/albums/12
```

**Response del ejemplo:**
```json
{
    "id": 12,
    "title": "Tanto Tango",
    "year": 1998,
    "band_id": 9
}
```

##### Errores de consulta
Si no se encuentra el álbum o banda con el ID especificado, se produce un error 404 (Not Found) junto con un mensaje descriptivo en el cuerpo de la respuesta.

**Ejemplo de request:**
```url
localhost/web2/TPE-WEB2-3/api/albums/1000
```

**Response del ejemplo:**
```json
"Album id=1000 not found"
```

### 2. Crear un álbum
Es posible crear un álbum mediante el endpoint  `POST /albums`.

Consideraciones:
- Para crear un álbum se debe poseer un token de autenticación, o se producirá un error 401 (Unauthorized).
- Si el álbum se crea correctamente, se muestra un código 201 (Created) y un mensaje descriptivo en el cuerpo de la respuesta.
- Al crear un álbum, el ID de la banda debe coincidir con una banda existente, de lo contrario se producirá un error 422 (Unprocessable Entity).
- El JSON enviado en el cuerpo del request debe contener los mismos campos que la entidad de álbum, excepto por su ID.

**Ejemplo de request:**
```url
POST localhost/web2/TPE-WEB2-3/api/albums
```
```json
{
    "title": "Nuevo álbum",
    "year": 2000,
    "band_id": 4
}
```

**Response del ejemplo:**
```json
"Album id=17 successfully created"
```

#### Errores
Los errores al crear un álbum pueden ser los siguientes:
- Error 401 (Unauthorized): Se produce cuando no se posee un token de autenticación válido.
```json
"Unauthorized"
```

- Error 422 (Unprocessable Entity): Se produce cuando el álbum no se puede crear exitosamente, ya sea porque la banda asignada no existe o por algún otro error en los valores del JSON enviado.
```json
"Band id=1000 does not exist"
```
```json
"Album id=20 could not be created"
```

### 3. Modificar un álbum
Es posible modificar un álbum existente mediante el endpoint  `PUT /albums/:id`

Consideraciones:
- Para modificar un álbum se debe poseer un token de autenticación, o se producirá un error 401 (Unauthorized).
- Si el álbum se modifica correctamente, se muestra un código 200 (OK) y un mensaje descriptivo en el cuerpo de la respuesta.
- Al modificar un álbum, el nuevo ID de la banda debe coincidir con una banda existente, de lo contrario se producirá un error 422 (Unprocessable Entity).
- El JSON enviado en el cuerpo del request debe contener los mismos campos que la entidad de álbum, excepto por su ID.

**Ejemplo de request:**
```url
PUT localhost/web2/TPE-WEB2-3/api/albums/17
```
```json
{
    "title": "Nuevo álbum modificado",
    "year": 2004,
    "band_id": 1
}
```

**Response del ejemplo:**
```json
"Album id=17 successfully modified"
```

#### Errores
Los errores al modificar un álbum pueden ser los siguientes:
- **Error 400 (Bad Request)**: Se produce cuando no se especifica el ID del álbum a modificar.
```json
"Album not specified"
```

- **Error 401 (Unauthorized)**: Se produce cuando no se posee un token de autenticación válido.
```json
"Unauthorized"
```

- **Error 404 (Not Found)**: Se produce cuando el álbum a modificar no existe.
```json
"Album id=523 does not exist"
```

- **Error 422 (Unprocessable Entity)**: Se produce cuando el álbum no se pudo modificar exitosamente, ya sea porque la nueva banda asignada no existe o por algún otro error en los valores del JSON enviado.
```json
"Band id=1000 does not exist"
```
```json
"Album id=17 could not be modified"
```

### 4. Eliminar un álbum
Es posible eliminar un álbum mediante el endpoint `DELETE /albums/:id`

Consideraciones:
- Si no se especifica el álbum a eliminar se produce un error 400 (Bad Request).
- Para eliminar un álbum se debe poseer un token de autenticación válido, o se producirá un error 401 (Unauthorized).
- Si el álbum se elimina correctamente se devuelve el código 200 (OK) y un mensaje descriptivo en el cuerpo de la respuesta.
- Si el álbum especificado no existe se produce un error 404 (Not Found).

**Ejemplo de request:**
```url
DELETE localhost/web2/TPE-WEB2-3/api/albums/17
```

**Response del ejemplo:**
```json
"Album id=17 deleted"
```

#### Errores
Los errores al eliminar un álbum pueden ser los siguientes:
- **Error 400 (Bad Request)**: Se produce cuando no se especifica el ID del álbum a eliminar.
```json
"Album not specified"
```

- **Error 401 (Unauthorized)**: Se produce cuando no se posee un token de autenticación válido.
```json
"Unauthorized"
```

- **Error 404 (Not Found)**: Se produce cuando el álbum a eliminar no existe.
```json
"Album id=523 not found"
```

---

## Autenticación y tokens

Para poder crear, modificar o eliminar álbumes, es necesario poseer un token válido de autenticación. El mismo se obtiene mediante el endpoint `GET user/token`, brindando un nombre de usuario y contraseña válidos.

El token obtenido es de formato JSON Web Token (JWT) y tiene un tiempo de expiración dado.

Aclaración: Para hacer pruebas, utilizar el siguiente usuario:
- username: webadmin
- password: admin

---
