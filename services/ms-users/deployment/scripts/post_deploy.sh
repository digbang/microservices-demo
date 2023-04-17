#!/bin/bash

cd /var/www/html

#print key
echo "Checking Key Variable"
printenv | grep KEY
printenv | grep ENV

echo "Decrypting env file"
# decrypt env file
php artisan env:decrypt --env=$ENV --key=$KEY --force

if [ $? -eq 0 ]; then
    # database table creation
    echo "replacing .env file instead .env.$ENV"
    mv .env.$ENV .env
    echo "Caching config"
    php artisan config:cache
    php artisan route:cache
    echo "Executing artisan migrate"
    php artisan migrate --force
    #TODO BORRAR TODOS LOS DEMAS

else
    echo "Something went wrong when decrypting env file"
fi

# Run the main container process
exec "$@"
