## Proyecto tienda del curso CITIA de ALKEMY
# Tienda en Línea - Entrega 1

¡Bienvenido al repositorio de la Tienda en Línea! Este proyecto es la primera entrega de la aplicación, enfocada en la gestión base del catálogo de productos y su organización.

## 🚀 Características de la Entrega 1

Por el momento, el sistema cuenta con los siguientes módulos funcionales:

* **Gestión de Categorías**: Creación, lectura, actualización y eliminación (CRUD) para organizar el catálogo.
* **Gestión de Productos**: Administración completa de los artículos de la tienda, vinculados a sus respectivas categorías.

## 🛠️ Tecnologías Utilizadas

* **PHP 8.4**
* **Laravel 13**
* **MySQL** / **PostgreSQL** (Base de datos)

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

## 👥 Autores
* **Julio Andres** - *Desarrollo Completo* - [JulioAndres2021](https://github.com/JulioAndres2021/proyecto-tienda)


