## Proyecto tienda del curso CITIA de ALKEMY
# Tienda en Línea - Entrega 2

¡Bienvenido al repositorio de la Tienda en Línea! Este proyecto es la segunda entrega de la aplicación, enfocada en la gestión base del catálogo de productos y su organización.

## 🚀 Características de la Entrega 2

Por el momento, el sistema cuenta con los siguientes módulos funcionales:

* **Gestión de Categorías**: Creación, lectura, actualización y eliminación (CRUD) para organizar el catálogo.
* **Gestión de Productos**: Administración completa de los artículos de la tienda, vinculados a sus respectivas categorías.

## 🛠️ Tecnologías Utilizadas

* **PHP 8.4**
* **Laravel 13**
* **MySQL**  (Base de datos)

## 📦 Instalación y Configuración Local

Seguí estos pasos para clonar y ejecutar el proyecto en tu entorno local:

### 1. Clonar el repositorio
```bash
git clone https://github.com
cd TU_REPOSITORIO
```

### 2. Instalar dependencias
```bash
composer install
npm install && npm run dev
```

### 3. Configurar el entorno
Copiá el archivo de ejemplo para crear tu archivo `.env`:
```bash
cp .env.example .env
```
Abrí el archivo `.env` y configurá las credenciales de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 4. Generar la clave de la aplicación
```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y seeders
Crea las tablas de Productos y Categorías en la base de datos (y datos de prueba si los incluiste):
```bash
php artisan migrate --seed
```

### 6. Iniciar el servidor local
```bash
php artisan serve
```
Ya podés acceder a la aplicación desde tu navegador en `http://127.0.0.1:8000`.

### 7. PRODUCTOS API

### Endpoints

| Method | Endpoint                     | Description          |
| ------ | ---------------------------- | -------------------- |
| GET    | /api/v1/productos            | List all products    |
| GET    | /api/v1/productos/{producto} | Get a single product |
| POST   | /api/v1/productos            | Create a new product |
| PUT    | /api/v1/productos/{producto} | Update a product     |
| DELETE | /api/v1/productos/{producto} | Delete a product     |

### Listar productos

GET /api/v1/productos

Response:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Productos obtenidos correctamente.",
	"datos": [
		{
			"id": 1,
			"sku": "123",
			"nombre": "Teléfono inteligente",
			"descripcion": "Un teléfono inteligente de última generación.",
			"precio": "699.99",
			"stock": 50,
			"categoria_id": 1,
			"created_at": "2026-08-11T12:35:50.000000Z",
			"updated_at": "2026-08-11T12:35:50.000000Z",
			"categoria": {
				"id": 1,
				"nombre": "Electrónica",
				"descripcion": "Productos electrónicos como teléfonos, computadoras, televisores, etc.",
				"created_at": "2026-08-11T12:35:50.000000Z",
				"updated_at": "2026-08-11T12:35:50.000000Z"
			}
		},
		{
			"id": 3,
			"sku": "125",
			"nombre": "Producto nuevo 4",
			"descripcion": "testeando el sistema.",
			"precio": "1000.50",
			"stock": 50,
			"categoria_id": 1,
			"created_at": "2026-08-11T12:49:30.000000Z",
			"updated_at": "2026-08-11T12:49:30.000000Z",
			"categoria": {
				"id": 1,
				"nombre": "Electrónica",
				"descripcion": "Productos electrónicos como teléfonos, computadoras, televisores, etc.",
				"created_at": "2026-08-11T12:35:50.000000Z",
				"updated_at": "2026-08-11T12:35:50.000000Z"
			}
		}
	]
}
```

GET /api/v1/productos/1

Response:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Producto obtenido correctamente.",
	"datos": {
		"id": 1,
		"sku": "123",
		"nombre": "Teléfono inteligente",
		"descripcion": "Un teléfono inteligente de última generación.",
		"precio": "699.99",
		"stock": 50,
		"categoria_id": 1,
		"created_at": "2026-08-11T12:35:50.000000Z",
		"updated_at": "2026-08-11T12:35:50.000000Z"
	}
}
```

GET /api/v1/productos/999

Response:

```json
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Producto no encontrado."
}
```

Status Code: 404 Not Found

### Crear un nuevo producto

POST /api/v1/productos

Request Body:

```json
{
	"exito": true,
	"codigo": 201,
	"mensaje": "Producto creado correctamente.",
	"datos": {
		"nombre": "Teléfono inteligente",
		"sku": "128",
		"descripcion": "Un teléfono inteligente de última generación.",
		"precio": "2000",
		"stock": 10,
		"categoria_id": 1,
		"updated_at": "2026-08-12T12:42:16.000000Z",
		"created_at": "2026-08-12T12:42:16.000000Z",
		"id": 5
	}
}
```

Status Code: 201 Created

### Actualizar un producto existente

PUT /api/v1/productos/1

Request Body:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Producto actualizado correctamente.",
	"datos": {
		"id": 1,
		"sku": "PROD-123",
		"nombre": "Teléfono inteligente editado",
		"descripcion": "editado Un teléfono inteligente de última generación.",
		"precio": "699.99",
		"stock": 50,
		"categoria_id": 1,
		"created_at": "2026-08-11T12:35:50.000000Z",
		"updated_at": "2026-08-12T13:24:39.000000Z"
	}
}
```
Actualizar un producto inexistente

PUT /api/v1/productos/33

```jason
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Producto no encontrado."
}
```

### Eliminar un producto

DELETE /api/v1/productos/1

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Producto eliminado correctamente."
}
```
Eliminar un producto inexistente

DELETE /api/v1/productos/33

```jason
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Producto no encontrado."
}
```

### 8. CATEGORIA API

### Endpoints

| Method | Endpoint                      | Description            |
| ------ | ----------------------------- | ---------------------- |
| GET    | /api/v1/categorias            | List all categorias    |
| GET    | /api/v1/categorias/{categoria}| Get a single categoria |
| POST   | /api/v1/categorias            | Create a new categoria |
| PUT    | /api/v1/categorias/{categoria}| Update a categoria     |
| DELETE | /api/v1/categorias/{categoria}| Delete a categoria     |

### Listar categorias

GET /api/v1/categorias

Response:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Categorías obtenidas correctamente.",
	"datos": [
		{
			"id": 1,
			"nombre": "Electrónica",
			"descripcion": "Productos electrónicos como teléfonos, computadoras, televisores, etc.",
			"created_at": "2026-08-11T12:35:50.000000Z",
			"updated_at": "2026-08-11T12:35:50.000000Z"
		},
		{
			"id": 4,
			"nombre": "INDUMENTARIA",
			"descripcion": "Ropa de vestir o calzado",
			"created_at": "2026-08-12T11:27:40.000000Z",
			"updated_at": "2026-08-12T11:27:40.000000Z"
		},
		{
			"id": 5,
			"nombre": "Categoria 5 EDITADA",
			"descripcion": "Ropa y Calzado",
			"created_at": "2026-08-12T11:28:12.000000Z",
			"updated_at": "2026-08-12T11:35:27.000000Z"
		},
		{
			"id": 7,
			"nombre": "INDUMENTARIA DEPORTIVA",
			"descripcion": null,
			"created_at": "2026-08-12T11:39:22.000000Z",
			"updated_at": "2026-08-12T11:39:22.000000Z"
		},
		{
			"id": 8,
			"nombre": "ELECTRONICA",
			"descripcion": "Video Grabadora",
			"created_at": "2026-08-12T11:40:56.000000Z",
			"updated_at": "2026-08-12T11:40:56.000000Z"
		}
	]
}
```

GET /api/v1/categorias/1

Response:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Categoría obtenida correctamente.",
	"datos": {
		"id": 1,
		"nombre": "Electrónica",
		"descripcion": "Productos electrónicos como teléfonos, computadoras, televisores, etc.",
		"created_at": "2026-08-11T12:35:50.000000Z",
		"updated_at": "2026-08-11T12:35:50.000000Z"
	}
}
```

GET /api/v1/categorias/999

Response:

```json
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Categoría no encontrada."
}
```

Status Code: 404 Not Found

### Crear una nueva categoria

POST /api/v1/categorias

Request Body:

```json
{
	"exito": true,
	"codigo": 201,
	"mensaje": "Categoría creada correctamente.",
	"datos": {
		"nombre": "Articulos Varios",
		"descripcion": "Productos varios...etc",
		"updated_at": "2026-08-12T15:18:38.000000Z",
		"created_at": "2026-08-12T15:18:38.000000Z",
		"id": 9
	}
}
```
Status Code: 201 Created

### Actualizar una categoria existente

PUT /api/v1/categorias/1

Request Body:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Categoría actualizada correctamente.",
	"datos": {
		"id": 1,
		"nombre": "Articulos Varios Editado",
		"descripcion": "Productos varios...etc, editado.!",
		"created_at": "2026-08-11T12:35:50.000000Z",
		"updated_at": "2026-08-12T15:24:22.000000Z"
	}
}
```
Actualizar una categoria inexistente

PUT /api/v1/categorias/33

```json
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Categoría no encontrada."
}
```

### Eliminar una categoria

DELETE /api/v1/categorias/1

Request Body:

```json
{
	"exito": true,
	"codigo": 200,
	"mensaje": "Categoría eliminada correctamente."
}
```

Eliminar una categoria inexsistente

DELETE /api/v1/categorias/11

Request Body

```json
{
	"exito": false,
	"codigo": 404,
	"mensaje": "Categoría no encontrada."
}
```








### Resultados de respuesta
```
| Código | Uso                                           |
| -----: | --------------------------------------------- |
|  `200` | Consulta, modificación o eliminación correcta |
|  `201` | Registro creado correctamente                 |
|  `401` | No autenticado                                |
|  `403` | No autorizado                                 |
|  `404` | Recurso no encontrado                         |
|  `422` | Error de validación                           |
|  `500` | Error interno inesperado                      |

```

### 9. FORM-REQUEST STORE

Estructuras de control para validar datos en el metodo STORE

StoreCategoriaRequest

```
public function rules(): array
    {
        return [
             'nombre' => ['required','string','max:255'],
             'descripcion' => ['required','string','max:200'],
        ];
    }

    //Mensajes personalizados para cada acción.
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
```   
StoreProductoRequest

```
public function rules(): array
    {
        return [

            'nombre' => ['required','string','max:255'],
            'sku' => ['required','string','unique:productos,sku'],
            'descripcion' => ['nullable','string','max:200'],
            'precio' => ['required','numeric','min:0'],
            'stock' => ['required','integer','min:0'],
            'categoria_id' => ['required','exists:categorias,id'],
        ];
    }

    //Mensajes personalizados para cada acción.
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU del producto es obligatorio.',
            'sku.unique' => 'El SKU ingresado ya pertenece a otro producto.',
            'precio.required' => 'El precio del producto es obligatorio.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'categoria_id.required' => 'La categoría del producto es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
```

### 9. FORM-REQUEST UPDATE

Estructuras de control para validar datos en el metodo UPDATE

UpdateCategoriaRequest

```
public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|required|string',
        ];
    }
```

UpdateProductoRequest

```
public function rules(): array
    {
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],

            'sku' => [
                'sometimes',
                'required',
                'string',
                new ValidSku(),
                Rule::unique('productos', 'sku')
                    ->ignore($this->route('producto')->id),
            ],

            'descripcion' => [
                'sometimes',
                'nullable',
                'string',
                'max:200'
            ],

            'precio' => [
                'sometimes',
                'required',
                'numeric',
                'min:0'
            ],

            'stock' => [
                'sometimes',
                'required',
                'integer',
                'min:0'
            ],

            'categoria_id' => [
                'sometimes',
                'required',
                'exists:categorias,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU del producto es obligatorio.',
            'sku.unique' => 'El SKU ingresado ya pertenece a otro producto.',
            'descripcion.string' => 'La descripción debe ser un texto.',
            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio no puede ser menor a 0.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor a 0.',
            'categoria_id.required' => 'La categoría del producto es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
```

## 👥 Autores
* **Julio Andres** - *Desarrollo Completo* - [JulioAndres2021](https://github.com/JulioAndres2021/proyecto-tienda)


