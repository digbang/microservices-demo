#!/bin/bash

# Eliminar el archivo .env y crearlo de nuevo
rm -f .env && touch .env

# Eliminar el archivo .env.example y crearlo de nuevo
rm -f .env.example && touch .env.example

# Eliminar directorio conf.d/default.conf y crearlo de nuevo de forma recursiva
rm -rf docker/nginx/conf.d/default.conf && touch docker/nginx/conf.d/default.conf

# Un mapa para guardar los nombres de los proyectos y sus directorios
declare -A project_directories

# Arreglo con los directorios de los archivos docker-compose.yml. El primer valor es el archivo "docker-compose.yml" de la carpeta raíz
docker_compose_files=("docker-compose.yml")

# Variable que almacena el valor inicial del puerto de la base de datos PGSQL
pgsql_port=5432

# Variable que almacena el valor inicial del puerto del server
server_port=81

# Recorrer todos los directorios de la carpeta services
for dir in services/*; do
    # Eliminar el valor de la variable project_name
    unset project_name

    # Si es un directorio
    if [ -d "$dir" ]; then
        # Existe el archivo .env
        if [ -f "$dir/.env" ]; then
            project_name=$(grep -E '^[^#]*PROJECT_NAME=' "$dir/.env" | sed -E 's/^[^=]*=//')
        fi

        # Si la variable project_name está vacía o no existe reemplazarla por el nombre del directorio
        if [ -z "$project_name" ]; then
            project_name=$(basename "$dir")
        fi

        # Agregar el path del archivo docker-compose.yml al array
        docker_compose_files+=("$dir/docker-compose.yml")

        # Agregar el nombre del proyecto y su directorio al mapa
        project_directories["$project_name"]="$dir"
    fi
done

# Unir los valores del arreglo con el separador de dos puntos y asignarlos a la variable joined_docker_compose_files
joined_docker_compose_files=$(IFS=:; echo "${docker_compose_files[*]}")

# Establecer el valor de la variable COMPOSE_FILE
echo "COMPOSE_FILE=$joined_docker_compose_files" >> .env

# Establecer el separador de dos puntos como valor de la variable COMPOSE_PATH_SEPARATOR
echo "COMPOSE_PATH_SEPARATOR=:" >> .env

# Recorrer todos los valores del mapa project_directories
for project_name in "${!project_directories[@]}"; do
    # Agregar una salto de línea al archivo .env
    echo "" >> .env

    # Obtener el directorio del proyecto y prefijarlo con "./" para que sea un path relativo
    project_dir="./${project_directories[$project_name]}"

    # Transformar el nombre del proyecto a mayúsculas y formatearlo para que sea una variable de entorno. Guardarlo en la variable env_project_name
    env_project_name=$(echo "$project_name" | tr '[:lower:]' '[:upper:]' | sed -E 's/[^A-Z0-9]+/_/g')

    # Establecer el valor de la variable "{env_project_name}_BASEPATH" con el valor de la variable project_dir
    echo "${env_project_name}_BASEPATH=$project_dir" >> .env

    # Establecer el valor de la variable "{env_project_name}_PORT" con el valor de la variable server_port y sumarle 1
    echo "${env_project_name}_PORT=$((server_port)):80" >> .env

    # Establecer el valor de la variable "{env_project_name}_DB_PORT" con el valor de la variable pgsql_port y sumarle 1
    echo "${env_project_name}_DB_PORT=$((pgsql_port++)):5432" >> .env

    # Clonar el .env del directorio raíz y llamarlo .env.example
    cp .env .env.example

    # Transformar el nombre del proyecto a dashed case. Guardarlo en la variable dashed_project_name
    dashed_project_name=$(echo "$project_name" | tr '[:upper:]' '[:lower:]' | sed -E 's/[^a-z0-9]+/-/g')

    # Obtener el contenido del archivo server-block.txt y reemplazar las ocurrencias de "project-name" por el valor de la variable dashed_project_name
    server_block=$(sed -E "s/project-name/$dashed_project_name/g" docker/nginx/conf.d/default.conf.template)

    # Reemplazar las ocurrencias de "server-port" por el valor de la variable server_port
    server_block=$(echo "$server_block" | sed -E "s/server-port/$server_port/g")

    # Agregar el contenido de la variable server_block al archivo docker/nginx/conf.d/default.conf
    echo "$server_block" >> docker/nginx/conf.d/default.conf

    # Agregar una salto de línea al archivo docker/nginx/conf.d/default.conf
    echo "" >> docker/nginx/conf.d/default.conf

    # Sumarle 1 a la variable server_port
    server_port=$((server_port+1))
done

# Eliminar último salto de línea del archivo docker/nginx/conf.d/default.conf
sed -i '$ d' docker/nginx/conf.d/default.conf
