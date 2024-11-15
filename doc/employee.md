# Employee API Spec

## Get Employees

Endpoint : GET /api/employees

Request Header :
- Authorization : Bearer token

Request Param :
- keyword (optional)
- direction (optional)
- per_page (optional)

Response Body :

```json
{
    "data": [
        {
            "name": "Muffin",
            "email": "muffin@example.com",
            "phone": "08987654321",
            "address": "Jl. Pengangsaan No. 72",
            "company_id": 1
        },
        {
            "name": "Shayne",
            "email": "shayne@example.com",
            "phone": "08876543219",
            "address": "Jl. Kota Baru No. 2A",
            "company_id": 2
        },
        {
            "name": "Ritz",
            "email": "ritz@example.com",
            "phone": "08765432198",
            "address": "Jl. Ir. H Juanda No. 142",
            "company_id": 3
        }
    ]
}
```

## Get Employee

Endpoint : GET /api/employees/:id

Request Header :
- Authorization : Bearer token

Response Body :

```json
{
    "data": {
        "name": "Muffin",
        "email": "muffin@example.com",
        "phone": "08987654321",
        "address": "Jl. Pengangsaan No. 72",
        "company_id": 1
    }
}
```

Response Body (Failed) :

```json
{
    "message": "employee not found"
}
```

## Create Employee

Endpoint : POST /api/employees

Request Header :
- Authorization : Bearer token

Request Body :

```json
{
    "name": "Muffin",
    "email": "muffin@example.com",
    "password": "muffin@password",
    "phone": "08987654321",
    "address": "Jl. Pengangsaan No. 72"
}
```

Response Body (Success) :

```json
{
    "message": "employee has been created"
}
```

Response Body (Failed) :

```json
{
    "errors": {
        "name": ["The field is required"],
        "email": ["The field is not valid email"]
    }
}
```

## Update Employee

Endpoint : PUT /api/employees/:id

Request Header :
- Authorization : Bearer token

Request Body :

```json
{
    "name": "Muffin",
    "email": "muffin@example.com",
    "phone": "089876543210",
    "address": "Jl. Pengangsaan No. 72"
}
```

Response Body (Success) :

```json
{
    "message": "employee has been updated"
}
```

Response Body (Failed) :

```json
{
    "errors": {
        "name": ["The field is required"],
        "email": ["The field is not valid email"]
    }
}
```

## Delete Employee

Endpoint : DELETE /api/employees/:id

Request Header :
- Authorization : Bearer token

Response Body (Success) :

```json
{
    "message": "employee has been deleted"
}
```
