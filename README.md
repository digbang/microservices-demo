# Microservices Demo

## Environment files
Copy the **.env.example** files on each service:

` cp services/ms-payments/.env.example services/ms-payments/.env `

` cp services/ms-users/.env.example services/ms-users/.env `

---

## Microservices Sync
` ./scripts/services-sync.sh `

---

## Building
` docker-compose up -d --build `

---

## Keys & Migrations
` docker-compose run ms-payments-php php artisan key:generate `

` docker-compose run ms-payments-php php artisan migrate `

` docker-compose run ms-users-php php artisan key:generate `

` docker-compose run ms-users-php php artisan migrate `

---

## API Gateway
Base URL: **http://localhost:8000**

### Services

- **Authentication**
  - GET /me
  - POST /login
  - POST /logout
  - POST /refresh

- **Users**
  - GET /users
  - POST /users
  - PATCH /users/{user_id}/enable
  - PATCH /users/{user_id}/disable

- **Payments**
  - POST /payments

---

## JSON Web Token (JWT)
Perform a GET request to http://localhost:8001/consumers/jwt_auth_issuer/jwt and you will get a similar JSON to the following one:

```
{
  "next": null,
  "data": [
    {
      "key": "********************************",
        "consumer": {
          "id": "0076ae4b-0648-5847-8ee6-1bf0a6049c97"
        },
        "rsa_public_key": null,
        "tags": null,
        "created_at": 1684847348,
        "id": "8fd98bab-9ff9-4661-8162-51e2b483e01f",
        "algorithm": "HS256",
        "secret": "********************************"
    }
  ]
}
```

- In the service ` ms-users ` replace the .env variable ` JWT_ISSUER ` value with the ` key ` value.

- In the service ` ms-users ` replace the .env variable ` JWT_SECRET ` value with the ` secret ` value.