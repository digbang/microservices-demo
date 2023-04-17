FROM postgres:14-alpine

ONBUILD RUN psql -U ms-users -d ms-users -c "create extension unaccent;"
