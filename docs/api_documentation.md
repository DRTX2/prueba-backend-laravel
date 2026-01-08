# Documentación de la API de Productos

## Descripción General

Esta API RESTful permite gestionar productos con soporte para múltiples divisas. Incluye operaciones CRUD completas para productos y gestión de precios en diferentes monedas.

## Base URL

```
http://localhost:8000/api
```

## Formato de Respuestas

Todas las respuestas siguen un formato JSON estandarizado:

```json
{
  "success": true|false,
  "message": "Mensaje descriptivo",
  "data": { ... } | [ ... ],
  "errors": { ... }  // Solo en caso de error de validación
}
```

## Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Operación exitosa |
| 201 | Recurso creado exitosamente |
| 404 | Recurso no encontrado |
| 422 | Error de validación |
| 500 | Error interno del servidor |

---

## Endpoints

### 1. Listar Productos

Obtiene una lista paginada de todos los productos.

**Request:**
```http
GET /api/products
```

**Parámetros de Query:**

| Parámetro | Tipo | Descripción | Default |
|-----------|------|-------------|---------|
| per_page | integer | Elementos por página (1-100) | 15 |
| page | integer | Número de página | 1 |

**Response (200):**
```json
{
  "success": true,
  "message": "Lista de productos obtenida exitosamente.",
  "data": [
    {
      "id": 1,
      "name": "Laptop HP ProBook 450",
      "description": "Laptop profesional con procesador Intel Core i7...",
      "price": 899.99,
      "currency": {
        "id": 1,
        "name": "Dólar Estadounidense",
        "symbol": "USD",
        "exchange_rate": 1.0
      },
      "tax_cost": 143.99,
      "manufacturing_cost": 450.0,
      "total_cost": 1493.98,
      "prices": [
        {
          "id": 1,
          "product_id": 1,
          "currency": {
            "id": 2,
            "name": "Euro",
            "symbol": "EUR",
            "exchange_rate": 0.92
          },
          "price": 827.99
        }
      ],
      "created_at": "2026-01-08T14:30:00+00:00",
      "updated_at": "2026-01-08T14:30:00+00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

### 2. Crear Producto

Crea un nuevo producto en el sistema.

**Request:**
```http
POST /api/products
Content-Type: application/json
```

**Body:**
```json
{
  "name": "Monitor LED 27 pulgadas",
  "description": "Monitor con panel IPS y resolución 4K",
  "price": 549.99,
  "currency_id": 1,
  "tax_cost": 87.99,
  "manufacturing_cost": 220.00
}
```

**Campos del Body:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| name | string | Sí | Nombre del producto (max: 255) |
| description | string | No | Descripción del producto (max: 5000) |
| price | decimal | Sí | Precio base (min: 0) |
| currency_id | integer | Sí | ID de la divisa base |
| tax_cost | decimal | No | Costo de impuestos (default: 0) |
| manufacturing_cost | decimal | No | Costo de fabricación (default: 0) |

**Response (201):**
```json
{
  "success": true,
  "message": "Producto creado exitosamente.",
  "data": {
    "id": 2,
    "name": "Monitor LED 27 pulgadas",
    "description": "Monitor con panel IPS y resolución 4K",
    "price": 549.99,
    "currency": {
      "id": 1,
      "name": "Dólar Estadounidense",
      "symbol": "USD",
      "exchange_rate": 1.0
    },
    "tax_cost": 87.99,
    "manufacturing_cost": 220.0,
    "total_cost": 857.98,
    "created_at": "2026-01-08T14:35:00+00:00",
    "updated_at": "2026-01-08T14:35:00+00:00"
  }
}
```

**Response (422) - Error de Validación:**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "name": ["El nombre del producto es obligatorio."],
    "price": ["El precio debe ser un valor numérico."],
    "currency_id": ["La divisa seleccionada no existe."]
  }
}
```

---

### 3. Obtener Producto por ID

Obtiene los detalles de un producto específico.

**Request:**
```http
GET /api/products/{id}
```

**Parámetros de Ruta:**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| id | integer | ID del producto |

**Response (200):**
```json
{
  "success": true,
  "message": "Producto obtenido exitosamente.",
  "data": {
    "id": 1,
    "name": "Laptop HP ProBook 450",
    "description": "Laptop profesional...",
    "price": 899.99,
    "currency": { ... },
    "tax_cost": 143.99,
    "manufacturing_cost": 450.0,
    "total_cost": 1493.98,
    "prices": [ ... ],
    "created_at": "2026-01-08T14:30:00+00:00",
    "updated_at": "2026-01-08T14:30:00+00:00"
  }
}
```

**Response (404):**
```json
{
  "success": false,
  "message": "Producto no encontrado."
}
```

---

### 4. Actualizar Producto

Actualiza los datos de un producto existente.

**Request:**
```http
PUT /api/products/{id}
Content-Type: application/json
```

**Body (parcial o completo):**
```json
{
  "name": "Laptop HP ProBook 450 G8",
  "price": 949.99,
  "tax_cost": 151.99
}
```

**Campos del Body:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| name | string | No | Nombre del producto |
| description | string | No | Descripción |
| price | decimal | No | Precio base |
| currency_id | integer | No | ID de la divisa base |
| tax_cost | decimal | No | Costo de impuestos |
| manufacturing_cost | decimal | No | Costo de fabricación |

**Response (200):**
```json
{
  "success": true,
  "message": "Producto actualizado exitosamente.",
  "data": {
    "id": 1,
    "name": "Laptop HP ProBook 450 G8",
    "price": 949.99,
    ...
  }
}
```

---

### 5. Eliminar Producto

Elimina un producto (soft delete).

**Request:**
```http
DELETE /api/products/{id}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Producto eliminado exitosamente."
}
```

**Response (404):**
```json
{
  "success": false,
  "message": "Producto no encontrado."
}
```

---

### 6. Listar Precios de un Producto

Obtiene los precios de un producto en diferentes divisas.

**Request:**
```http
GET /api/products/{id}/prices
```

**Response (200):**
```json
{
  "success": true,
  "message": "Precios del producto obtenidos exitosamente.",
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "currency": {
        "id": 2,
        "name": "Euro",
        "symbol": "EUR",
        "exchange_rate": 0.92
      },
      "price": 827.99,
      "created_at": "2026-01-08T14:30:00+00:00",
      "updated_at": "2026-01-08T14:30:00+00:00"
    },
    {
      "id": 2,
      "product_id": 1,
      "currency": {
        "id": 3,
        "name": "Peso Mexicano",
        "symbol": "MXN",
        "exchange_rate": 17.15
      },
      "price": 15434.82,
      "created_at": "2026-01-08T14:30:00+00:00",
      "updated_at": "2026-01-08T14:30:00+00:00"
    }
  ]
}
```

---

### 7. Crear Precio para un Producto

Registra el precio de un producto en una nueva divisa.

**Request:**
```http
POST /api/products/{id}/prices
Content-Type: application/json
```

**Body:**
```json
{
  "currency_id": 2,
  "price": 827.99
}
```

**Campos del Body:**

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| currency_id | integer | Sí | ID de la divisa |
| price | decimal | Sí | Precio en la divisa especificada |

**Response (201):**
```json
{
  "success": true,
  "message": "Precio del producto creado exitosamente.",
  "data": {
    "id": 5,
    "product_id": 1,
    "currency": {
      "id": 2,
      "name": "Euro",
      "symbol": "EUR",
      "exchange_rate": 0.92
    },
    "price": 827.99,
    "created_at": "2026-01-08T14:40:00+00:00",
    "updated_at": "2026-01-08T14:40:00+00:00"
  }
}
```

**Response (422) - Precio Duplicado:**
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "currency_id": ["Ya existe un precio para este producto en la divisa seleccionada."]
  }
}
```

---

## Divisas Disponibles (Seeders)

| ID | Nombre | Símbolo | Tasa de Cambio |
|----|--------|---------|----------------|
| 1 | Dólar Estadounidense | USD | 1.000000 |
| 2 | Euro | EUR | 0.920000 |
| 3 | Peso Mexicano | MXN | 17.150000 |
| 4 | Peso Colombiano | COP | 3950.000000 |
| 5 | Libra Esterlina | GBP | 0.790000 |

---

## Notas Técnicas

1. **Soft Deletes**: Los productos eliminados no se borran físicamente, se marcan con `deleted_at`.
2. **Costo Total**: El campo `total_cost` es calculado automáticamente como: `price + tax_cost + manufacturing_cost`.
3. **Unicidad de Precios**: No puede existir más de un precio para el mismo producto y divisa.
4. **Paginación**: El máximo de elementos por página es 100.
