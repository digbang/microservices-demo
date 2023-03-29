#!/bin/bash
rm -fr ./docker/proxy/conf.d/default.conf
rm -fr ./.env

declare -A project_names=()
declare -l composer_path_separator=":"
declare -a microservice_paths=()
declare -i port=80
declare -i db_port=5432
declare -i db_internal_port=5432

for file in src/* ; do
    project_name=""

    if [ -d $file ] ; then
        microservice_paths+=($file)

        if [ -f "$file/.env" ] ; then
            project_name=$(cat "$file/.env" | grep "^[^#]" "$file/.env" | grep -w PROJECT_NAME | cut -d "=" -f 2)
        fi

        if [ ! $project_name ] ; then
            project_name=$(basename -- $file)
        fi

        project_names+=( [${project_name//"-"/"_"}]="./$file" )
    fi
done

# Setup COMPOSER_PATH_SEPARATOR env variable, default to ":"
echo "COMPOSE_PATH_SEPARATOR=$composer_path_separator" >> .env

# Setup COMPOSER_FILE env variable indicating where each microservice is placed
if [ microservice_paths ] ; then
    # Join
    printf -v composer_paths "%s/docker-compose.yml$composer_path_separator" "${microservice_paths[@]}"

    echo "COMPOSE_FILE=docker-compose.yml$composer_path_separator$composer_paths" >> .env

    # Remove the last ; from the file
    sed -i "$ s/.$//" .env
fi

# Setup every microservice *_BASEPATH, *_PORT and *_DB_PORT in order to avoid colissions
for project_name in ${!project_names[@]} ; do
    echo >> .env
    echo "${project_name^^}_BASEPATH=${project_names[$project_name]}" >> .env
    echo "${project_name^^}_PORT=$port" >> .env
    echo "${project_name^^}_DB_PORT=$((db_port++)):$db_internal_port" >> .env
done

# Generate each conf.d/default.conf server block
for project_name in ${!project_names[@]} ; do
    project_name_formatted=${project_name//"_"/"-"}

    server_block=$(cat server-block.txt)
    server_block=${server_block//project-name/$project_name_formatted}

    echo "$server_block" >> docker/proxy/conf.d/default.conf
done
