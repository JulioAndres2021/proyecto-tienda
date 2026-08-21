# Proyecto Tienda - API Laravel

API REST desarrollada en Laravel para una tienda. Incluye CRUD de
categorías y productos, carrito persistente, resumen de compra,
checkout, DTOs, manejo de inventario, respuestas JSON y colección de
Postman.

> **Nota:** Los IDs, precios, stock, fechas y datos mostrados en las
> respuestas son ejemplos y pueden variar según la base de datos.

## Instalación

``` bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Configurar previamente MySQL en `.env`.

Base URL:

``` text
http://127.0.0.1:8000/api/v1
```

## Formato general

Respuesta exitosa:

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Operación realizada correctamente.",
  "datos": {}
}
```

Respuesta de error:

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Los datos enviados no son válidos.",
  "errores": {}
}
```

  Código   Significado
  -------- -------------------------------
  200      Operación exitosa
  201      Recurso creado
  404      Recurso o ruta no encontrada
  405      Método HTTP no permitido
  422      Validación o regla de negocio
  500      Error interno

# Categorías

## GET `/categorias`

Lista las categorías.

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Categorías obtenidas correctamente.",
  "datos": [
    {
      "id": 1,
      "nombre": "Tecnología",
      "descripcion": "Productos tecnológicos"
    }
  ]
}
```

## GET `/categorias/{id}`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Categoría obtenida correctamente.",
  "datos": {
    "id": 1,
    "nombre": "Tecnología",
    "descripcion": "Productos tecnológicos"
  }
}
```

**Respuesta 404**

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "Recurso no encontrado."
}
```

## POST `/categorias`

Body de ejemplo:

``` json
{
  "nombre": "Electrónica",
  "descripcion": "Productos electrónicos"
}
```

**Respuesta 201**

``` json
{
  "exito": true,
  "codigo": 201,
  "mensaje": "Categoría creada correctamente.",
  "datos": {
    "id": 3,
    "nombre": "Electrónica",
    "descripcion": "Productos electrónicos"
  }
}
```

**Validación 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Los datos enviados no son válidos.",
  "errores": {
    "nombre": ["El campo nombre es obligatorio."]
  }
}
```

## PUT `/categorias/{id}`

Body:

``` json
{
  "nombre": "Electrónica actualizada",
  "descripcion": "Descripción actualizada"
}
```

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Categoría actualizada correctamente.",
  "datos": {
    "id": 3,
    "nombre": "Electrónica actualizada",
    "descripcion": "Descripción actualizada"
  }
}
```

## DELETE `/categorias/{id}`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Categoría eliminada correctamente."
}
```

# Productos

## GET `/productos`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Productos obtenidos correctamente.",
  "datos": [
    {
      "id": 9,
      "sku": "PROD-288",
      "nombre": "Producto ejemplo",
      "descripcion": "Descripción del producto",
      "precio": "477.18",
      "stock": 38,
      "categoria_id": 1
    }
  ]
}
```

## GET `/productos/{id}`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Producto obtenido correctamente.",
  "datos": {
    "id": 9,
    "sku": "PROD-288",
    "nombre": "Producto ejemplo",
    "descripcion": "Descripción del producto",
    "precio": "477.18",
    "stock": 38,
    "categoria_id": 1
  }
}
```

**Respuesta 404**

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "Recurso no encontrado."
}
```

## POST `/productos`

Body:

``` json
{
  "sku": "PROD-100",
  "nombre": "Teclado",
  "descripcion": "Teclado de ejemplo",
  "precio": 15000,
  "stock": 10,
  "categoria_id": 1
}
```

**Respuesta 201**

``` json
{
  "exito": true,
  "codigo": 201,
  "mensaje": "Producto creado correctamente.",
  "datos": {
    "id": 10,
    "sku": "PROD-100",
    "nombre": "Teclado",
    "descripcion": "Teclado de ejemplo",
    "precio": "15000.00",
    "stock": 10,
    "categoria_id": 1
  }
}
```

## PUT `/productos/{id}`

Body:

``` json
{
  "sku": "PROD-100",
  "nombre": "Teclado actualizado",
  "descripcion": "Nueva descripción",
  "precio": 16000,
  "stock": 15,
  "categoria_id": 1
}
```

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Producto actualizado correctamente.",
  "datos": {
    "id": 10,
    "sku": "PROD-100",
    "nombre": "Teclado actualizado",
    "descripcion": "Nueva descripción",
    "precio": "16000.00",
    "stock": 15,
    "categoria_id": 1
  }
}
```

## DELETE `/productos/{id}`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Producto eliminado correctamente."
}
```

# Carrito

El carrito se persiste en base de datos. Se identifica mediante:

``` text
X-Carrito-Token: {{carrito_token}}
```

La primera petición para agregar un producto puede realizarse sin token.
La API crea el carrito y devuelve `token_carrito`. En las siguientes
peticiones debe enviarse ese mismo token.

## GET `/carrito`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Carrito obtenido correctamente.",
  "token_carrito": "6e03b220-2d6f-4e27-8800-0dbd73734bb0",
  "datos": {
    "id": 15,
    "estado": "activo",
    "items": [
      {
        "id": 14,
        "carrito_id": 15,
        "producto_id": 9,
        "cantidad": 3,
        "precio_unitario": "477.18"
      }
    ]
  }
}
```

**Respuesta 404**

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "No se encontró el carrito."
}
```

## POST `/carrito/productos`

Body:

``` json
{
  "producto_id": 9,
  "cantidad": 2
}
```

**Respuesta 201**

``` json
{
  "exito": true,
  "codigo": 201,
  "mensaje": "Producto agregado al carrito.",
  "token_carrito": "6e03b220-2d6f-4e27-8800-0dbd73734bb0",
  "datos": {
    "carrito_id": 15,
    "producto_id": 9,
    "cantidad": 2,
    "precio_unitario": "477.18"
  }
}
```

Si se agrega nuevamente el mismo producto usando el mismo token, la
cantidad se suma.

**Cantidad inválida 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Los datos enviados no son válidos.",
  "errores": {
    "cantidad": ["La cantidad debe ser al menos 1."]
  }
}
```

**Stock insuficiente 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Stock insuficiente.",
  "errores": {
    "stock": ["Stock disponible: 38."]
  }
}
```

## PUT `/carrito/productos/{producto}`

Body:

``` json
{
  "cantidad": 4
}
```

La cantidad reemplaza a la actual.

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Cantidad actualizada correctamente.",
  "datos": {
    "producto_id": 9,
    "cantidad": 4,
    "precio_unitario": "477.18"
  }
}
```

## DELETE `/carrito/productos/{producto}`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Producto eliminado del carrito."
}
```

## DELETE `/carrito`

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Carrito vaciado correctamente."
}
```

# Resumen de compra

## GET `/carrito/resumen`

Header:

``` text
X-Carrito-Token: {{carrito_token}}
```

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Resumen de compra calculado correctamente.",
  "datos": {
    "subtotal": 2385.90,
    "impuestos": 501.04,
    "costo_envio": 5000,
    "total": 7886.94
  }
}
```

Reglas:

-   `subtotal`: suma de cantidad por precio unitario.
-   `impuestos`: 21% del subtotal.
-   `costo_envio`: \$5000 si el subtotal es mayor a 0 y menor a \$50000.
-   Envío gratis si el subtotal es igual o superior a \$50000.
-   `total`: subtotal + impuestos + costo de envío.

# Checkout

Flujo:

1.  Revisar carrito.
2.  Registrar datos de envío y pago.
3.  Confirmar compra.

## GET `/checkout/revisar`

Valida carrito, productos y stock.

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Carrito listo para continuar con la compra.",
  "datos": {
    "items": [
      {
        "producto_id": 9,
        "cantidad": 3,
        "precio_unitario": "477.18"
      }
    ],
    "resumen": {
      "subtotal": 1431.54,
      "impuestos": 300.62,
      "costo_envio": 5000,
      "total": 6732.16
    }
  }
}
```

**Carrito vacío 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "El carrito está vacío."
}
```

## POST `/checkout/datos`

Body:

``` json
{
  "nombre_cliente": "Cliente 1",
  "email": "cliente1@gmail.com",
  "direccion_envio": "Calle Sin Numero",
  "ciudad": "General Pico",
  "codigo_postal": "6360",
  "metodo_pago": "efectivo"
}
```

Métodos permitidos: `tarjeta`, `transferencia`, `efectivo`.

**Respuesta 200**

``` json
{
  "exito": true,
  "codigo": 200,
  "mensaje": "Datos de checkout registrados correctamente.",
  "datos": {
    "carrito_id": 15,
    "nombre_cliente": "Cliente 1",
    "email": "cliente1@gmail.com",
    "direccion_envio": "Calle Sin Numero",
    "ciudad": "General Pico",
    "codigo_postal": "6360",
    "metodo_pago": "efectivo"
  }
}
```

**Método de pago inválido 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Los datos enviados no son válidos.",
  "errores": {
    "metodo_pago": [
      "El campo metodo pago no está en la lista de valores permitidos."
    ]
  }
}
```

## POST `/checkout/confirmar`

No requiere body.

Antes de confirmar se valida nuevamente el stock. Luego se registra la
compra y sus detalles, se descuenta el inventario y el carrito cambia a
estado `comprado`. La operación se realiza dentro de una transacción.

**Respuesta 201**

``` json
{
  "exito": true,
  "codigo": 201,
  "mensaje": "Compra confirmada correctamente.",
  "datos": {
    "compra_id": 1,
    "estado": "confirmada",
    "subtotal": 1431.54,
    "impuestos": 300.62,
    "costo_envio": 5000,
    "total": 6732.16,
    "detalles": [
      {
        "producto_id": 9,
        "nombre_producto": "Producto ejemplo",
        "cantidad": 3,
        "precio_unitario": 477.18,
        "subtotal": 1431.54
      }
    ]
  }
}
```

**Checkout sin datos 422**

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Primero debe registrar los datos de checkout."
}
```

# DTOs

## `DatosCheckoutDTO`

Archivo:

``` text
app/DTOs/DatosCheckoutDTO.php
```

Estructura los datos de entrada del checkout:

-   `nombreCliente`
-   `email`
-   `direccionEnvio`
-   `ciudad`
-   `codigoPostal`
-   `metodoPago`

Uso:

``` php
$dto = DatosCheckoutDTO::desdeArray($request->validated());
$datos = $dto->toArray();
```

## `CompraConfirmadaDTO`

Archivo:

``` text
app/DTOs/CompraConfirmadaDTO.php
```

Estructura la respuesta de una compra confirmada:

-   `compraId`
-   `estado`
-   `subtotal`
-   `impuestos`
-   `costoEnvio`
-   `total`
-   `detalles`

Uso:

``` php
$dto = CompraConfirmadaDTO::desdeCompra($compra);
```

# Inventario

El stock se valida:

1.  Al agregar un producto al carrito.
2.  Al actualizar una cantidad.
3.  Al revisar el checkout.
4.  Antes de confirmar la compra.

Al confirmar:

``` text
stock_nuevo = stock_actual - cantidad_comprada
```

# Errores documentados

## Producto inexistente - 404

``` http
GET /productos/999999
```

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "Recurso no encontrado."
}
```

## Cantidad inválida - 422

``` http
POST /carrito/productos
```

Body:

``` json
{
  "producto_id": 9,
  "cantidad": 0
}
```

Respuesta:

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Los datos enviados no son válidos.",
  "errores": {
    "cantidad": ["La cantidad debe ser al menos 1."]
  }
}
```

## Stock insuficiente - 422

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Stock insuficiente.",
  "errores": {
    "stock": ["Stock disponible: 38."]
  }
}
```

## Checkout sin datos - 422

``` http
POST /checkout/confirmar
```

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Primero debe registrar los datos de checkout."
}
```

## Carrito inexistente - 404

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "No se encontró el carrito."
}
```

## Ruta inexistente - 404

``` http
GET /ruta-inexistente
```

``` json
{
  "exito": false,
  "codigo": 404,
  "mensaje": "Ruta no encontrada."
}
```

## Método HTTP inválido - 405

La ruta válida es:

``` http
GET /checkout/revisar
```

Ejemplo incorrecto:

``` http
POST /checkout/revisar
```

Respuesta:

``` json
{
  "exito": false,
  "codigo": 405,
  "mensaje": "Método HTTP no permitido para esta ruta."
}
```

# Persistencia del carrito

El carrito se almacena en base de datos y se identifica mediante un UUID
enviado en `X-Carrito-Token`. Al confirmar una compra, su estado pasa de
`activo` a `comprado`.

# Base de datos

Tablas principales:

-   `categorias`
-   `productos`
-   `carritos`
-   `item_carritos`
-   `datos_checkout`
-   `compras`
-   `detalles_compra`

# Postman

Colección:

``` text
postman/Tienda_API.postman_collection.json
```

Variables:

-   `base_url`
-   `X-Carrito_Token`
-   `producto`

Organización sugerida:

``` text
Tienda API
├── Categorias
├── Productos
├── Carrito
├── Resumen
├── Checkout
└── Errores
```

# Resumen de endpoints

  Método   Endpoint                          Descripción
  -------- --------------------------------- ------------------------
  GET      `/categorias`                     Listar categorías
  GET      `/categorias/{id}`                Mostrar categoría
  POST     `/categorias`                     Crear categoría
  PUT      `/categorias/{id}`                Actualizar categoría
  DELETE   `/categorias/{id}`                Eliminar categoría
  GET      `/productos`                      Listar productos
  GET      `/productos/{id}`                 Mostrar producto
  POST     `/productos`                      Crear producto
  PUT      `/productos/{id}`                 Actualizar producto
  DELETE   `/productos/{id}`                 Eliminar producto
  GET      `/carrito`                        Mostrar carrito
  POST     `/carrito/productos`              Agregar producto
  PUT      `/carrito/productos/{producto}`   Actualizar cantidad
  DELETE   `/carrito/productos/{producto}`   Eliminar producto
  DELETE   `/carrito`                        Vaciar carrito
  GET      `/carrito/resumen`                Obtener resumen
  GET      `/checkout/revisar`               Revisar carrito
  POST     `/checkout/datos`                 Registrar envío y pago
  POST     `/checkout/confirmar`             Confirmar compra

# Flujo de compra

``` text
Producto
   ↓
Agregar al carrito
   ↓
Validar stock
   ↓
Actualizar / eliminar
   ↓
Resumen
   ↓
Revisar checkout
   ↓
Registrar envío y pago
   ↓
Confirmar
   ↓
Validar stock nuevamente
   ↓
Crear compra y detalles
   ↓
Descontar stock
   ↓
Carrito = comprado
```

## 👥 Autor
* **Julio Andres** - *Desarrollo Completo* - [JulioAndres2021](https://github.com/JulioAndres2021/proyecto-tienda)


