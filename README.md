# Setup
1. cp ./src/ms-payments/.env.example ./src/ms-payments/.env
   
2. cp ./src/ms-users/.env.example ./src/ms-users/.env
   
3. ./setup.sh
   
4. docker-compose up -d --build

5. docker-compose exec ms-users-php bash
   1. php artisan key:generate
   2. php artisan migrate

6. docker-compose exec ms-payments-php bash
   1. php artisan key:generate
   2. php artisan migrate

## Containers
1. Payments
   - docker-compose exec ms-payments-php bash
   - docker-compose exec ms-payments-db bash

2. Users
   - docker-compose exec ms-users-php bash
   - docker-compose exec ms-users-db bash

## Host
This should be added to the host file:

127.0.0.1 ms-payments.demo
127.0.0.1 ms-users.demo

## API URL
- http://ms-payments.demo
- http://ms-users.demo

## Consuming RabbitMQ
In order to handle payment registration event you must execute the following command in the users service:
php artisan rabbitmq:consume "App\RabbitMQ\Handler\PaymentRegisteredHandler"
