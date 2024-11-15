# Auth API Spec

## Login

Endpoint : POST /api/auth/login

Request Body :

```json
{
    "email": "muffin@example.com",
    "password": "muffin@password",
}
```

Response Body (Success) :

```json
{
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ",
    "token_type": "bearer",
    "expires_in": 3600,
}
```

Response Body (Failed) :

```json
{
    "message": "invalid email or password"
}
```
