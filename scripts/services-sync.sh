#!/bin/bash

# Eliminar el archivo .env y crearlo de nuevo
rm -f .env && touch .env

# Eliminar el archivo .env.example
rm -f .env.example

# Un mapa para guardar los nombres de los proyectos y sus directorios
declare -A project_directories

# Arreglo con los directorios de los archivos docker-compose.yml. El primer valor es el archivo "docker-compose.yml" de la carpeta raíz
docker_compose_files=("docker-compose.yml")

# Variable que almacena el valor inicial del puerto de la base de datos PGSQL
pgsql_port=5432

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

    # Establecer el valor de la variable "{env_project_name}_DB_PORT" con el valor de la variable pgsql_port y sumarle 1
    echo "${env_project_name}_DB_PORT=$((pgsql_port++)):5432" >> .env

    # Clonar el .env del directorio raíz y llamarlo .env.example
    cp -p .env .env.example
done
