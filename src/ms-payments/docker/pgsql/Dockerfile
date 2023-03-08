FROM postgres:14-alpine

ONBUILD RUN psql -U witwot-ms-auth -d witwot-ms-auth -c "create extension unaccent;"
