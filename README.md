# Proyecto Tienda - API Laravel

API REST desarrollada en Laravel para la gestión básica de una tienda.

## Funcionalidades

-   CRUD de categorías y productos.
-   Carrito persistente mediante `X-Carrito-Token`.
-   Resumen de compra.
-   Checkout.
-   Validación y descuento de stock.
-   DTOs.
-   Respuestas JSON estandarizadas.
-   Colección de Postman.

## Instalación

``` bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Configurar previamente la conexión MySQL en `.env`.

Base de la API:

``` text
http://127.0.0.1:8000/api/v1
```

## Categorías

  Método   Endpoint             Descripción
  -------- -------------------- ----------------------
  GET      `/categorias`        Listar categorías
  GET      `/categorias/{id}`   Mostrar categoría
  POST     `/categorias`        Crear categoría
  PUT      `/categorias/{id}`   Actualizar categoría
  DELETE   `/categorias/{id}`   Eliminar categoría

## Productos

  Método   Endpoint            Descripción
  -------- ------------------- ---------------------
  GET      `/productos`        Listar productos
  GET      `/productos/{id}`   Mostrar producto
  POST     `/productos`        Crear producto
  PUT      `/productos/{id}`   Actualizar producto
  DELETE   `/productos/{id}`   Eliminar producto

## Carrito

El carrito se persiste en base de datos y se identifica mediante el
header:

``` text
X-Carrito-Token: TOKEN
```

La primera vez que se agrega un producto la API genera el token. Debe
reutilizarse en las peticiones siguientes.

  Método   Endpoint                          Descripción
  -------- --------------------------------- ---------------------
  GET      `/carrito`                        Mostrar carrito
  POST     `/carrito/productos`              Agregar producto
  PUT      `/carrito/productos/{producto}`   Actualizar cantidad
  DELETE   `/carrito/productos/{producto}`   Eliminar producto
  DELETE   `/carrito`                        Vaciar carrito

Ejemplo para agregar:

``` json
{
  "producto_id": 9,
  "cantidad": 2
}
```

Si el producto ya existe en el carrito, la cantidad enviada se suma a la
actual. El stock se valida antes de agregar o actualizar.

## Resumen de compra

``` http
GET /carrito/resumen
```

Devuelve `subtotal`, `impuestos`, `costo_envio` y `total`.

Reglas utilizadas:

-   Impuestos: 21% del subtotal.
-   Envío: \$5000 si el subtotal es mayor a 0 y menor a \$50000.
-   Envío gratis desde \$50000.

Ejemplo:

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

## Checkout

El flujo es:

1.  Revisar carrito.
2.  Registrar datos de envío y pago.
3.  Confirmar compra.

### Revisar

``` http
GET /checkout/revisar
```

Valida que el carrito exista, tenga productos y disponga de stock.

### Registrar datos

``` http
POST /checkout/datos
```

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

### Confirmar

``` http
POST /checkout/confirmar
```

Al confirmar se vuelve a validar el stock, se registra la compra y sus
detalles, se descuenta inventario y el carrito pasa a estado `comprado`.

## DTOs

### DatosCheckoutDTO

`app/DTOs/DatosCheckoutDTO.php`

Estructura los datos de entrada del checkout: nombre, email, dirección,
ciudad, código postal y método de pago.

``` php
DatosCheckoutDTO::desdeArray($datos);
$dto->toArray();
```

### CompraConfirmadaDTO

`app/DTOs/CompraConfirmadaDTO.php`

Estructura la respuesta de una compra confirmada, incluyendo
identificador, estado, importes y detalles.

``` php
CompraConfirmadaDTO::desdeCompra($compra);
```

## Inventario

El stock se valida al agregar/actualizar productos y nuevamente antes de
confirmar. Al confirmar se descuenta la cantidad comprada.

Ejemplo:

``` json
{
  "exito": false,
  "codigo": 422,
  "mensaje": "Stock insuficiente.",
  "errores": {
    "stock": ["Stock disponible: 10."]
  }
}
```

## Respuestas JSON

Las respuestas utilizan JSON y códigos HTTP adecuados.

  Código   Significado
  -------- -------------------------------
  200      Operación exitosa
  201      Recurso creado
  404      Recurso o ruta no encontrada
  405      Método HTTP no permitido
  422      Validación o regla de negocio
  500      Error interno

## Base de datos

Tablas principales:

-   `categorias`
-   `productos`
-   `carritos`
-   `item_carritos`
-   `datos_checkout`
-   `compras`
-   `detalles_compra`

## Postman

La colección se entrega en:

``` text
postman/Tienda_API.postman_collection.json
```

Variables:

-   `base_url`
-   `carrito_token`

Incluye pruebas de Categorías, Productos, Carrito, Resumen, Checkout y
errores.

Casos de error documentados:

-   Producto inexistente (`404`).
-   Cantidad inválida (`422`).
-   Stock insuficiente (`422`).
-   Checkout sin datos (`422`).
-   Ruta inexistente (`404`).
-   Método HTTP inválido (`405`).

## Flujo general

``` text
Producto
  ↓
Agregar al carrito
  ↓
Actualizar / eliminar
  ↓
Resumen
  ↓
Revisar checkout
  ↓
Registrar envío y pago
  ↓
Confirmar compra
  ↓
Registrar compra y detalles
  ↓
Descontar stock
  ↓
Carrito comprado
```


## 👥 Autores
* **Julio Andres** - *Desarrollo Completo* - [JulioAndres2021](https://github.com/JulioAndres2021/proyecto-tienda)


