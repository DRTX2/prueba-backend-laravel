# API de Gestión de Productos

API RESTful desarrollada en Laravel para la gestión de productos con soporte multi-divisa.

## Requisitos

- PHP 8.2 o superior
- Composer 2.x
- PostgreSQL 14+ (Local) o Docker Desktop (Sail)
- Laravel 12.x

## Instalación

```bash
# Clonar e instalar dependencias
git clone <url-del-repositorio>
cd prueba-backend-laravel
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env y ejecutar migraciones
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

### Usando Docker (Laravel Sail)

Si prefieres usar Docker, puedes usar Laravel Sail:

```bash
# Iniciar contenedores
./vendor/bin/sail up -d

# Ejecutar migraciones y seeders dentro del contenedor
./vendor/bin/sail artisan migrate --seed

# Ejecutar tests
./vendor/bin/sail test
```

La API estará disponible en `http://localhost`.

La API estará disponible en `http://localhost:8000/api`

## Modelo de Datos

### Tablas

| Tabla | Descripción |
|-------|-------------|
| `currencies` | Divisas disponibles (USD, EUR, MXN, etc.) |
| `products` | Productos con precio base, impuestos y costo de fabricación |
| `product_prices` | Precios de productos en diferentes divisas |

## Endpoints de la API

### Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/products` | Listar todos los productos |
| POST | `/api/products` | Crear un nuevo producto |
| GET | `/api/products/{id}` | Obtener un producto por ID |
| PUT | `/api/products/{id}` | Actualizar un producto |
| DELETE | `/api/products/{id}` | Eliminar un producto |

### Precios de Productos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/products/{id}/prices` | Listar precios de un producto |
| POST | `/api/products/{id}/prices` | Crear precio en nueva divisa |

## Pruebas

```bash
php artisan test
```

### Cobertura de Pruebas

- ✅ Listar productos (paginación)
- ✅ Crear productos con validaciones
- ✅ Obtener producto por ID
- ✅ Actualizar productos
- ✅ Eliminar productos (soft delete)
- ✅ Gestión de precios multi-divisa
- ✅ Validación de campos requeridos
- ✅ Manejo de errores 404

## Documentación

- **Insomnia Collection**: `docs/insomnia_collection.json`
- **Swagger/OpenAPI**: `docs/openapi.yaml`
- **Documentación detallada**: `docs/api_documentation.md`