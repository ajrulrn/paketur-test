# Manager API Spec

## Get Managers

Endpoint : GET /api/managers

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
            "company_id": 1
        },
        {
            "name": "Shayne",
            "email": "shayne@example.com",
            "company_id": 2
        },
        {
            "name": "Ritz",
            "email": "ritz@example.com",
            "company_id": 3
        }
    ]
}
```

## Get Manager

Endpoint : GET /api/managers/:id

Request Header :
- Authorization : Bearer token

Response Body :

```json
{
    "data": {
        "name": "Muffin",
        "email": "muffin@example.com",
        "company_id": 1
    }
}
```

Response Body (Failed) :

```json
{
    "message": "manager not found"
}
```
