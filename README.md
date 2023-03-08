# Setup
1. cp .env.example .env
2. cp ./src/ms-payments/.env.example ./src/ms-payments/.env
3. cp ./src/ms-users/.env.example ./src/ms-users/.env
4. docker-compose up -d --build

## Containers
1. Payments
   - cd ./src/ms-payments
   - docker-compose exec payments-api-php bash
   - docker-compose exec payments-api-db bash

2. Users
   - cd ./src/ms-users
   - docker-compose exec users-api-php bash
   - docker-compose exec users-api-db bash

## New Services
1. open .env file
2. add the new COMPOSE_FILE path, for example: src/{service}/docker-compose.yml
3. add the new service basepath, for example {SERVICE}_API_BASEPATH=./src/{service}
4. add the new service database port if needed, for example (5432 is the pgsql default): {SERVICE}_API_DB_PORT={port}:5432


## Consuming RabbitMQ
In order to handle payment registration event you must execute the following command in the users service:
php artisan rabbitmq:consume "App\RabbitMQ\Handler\PaymentRegisteredHandler"
