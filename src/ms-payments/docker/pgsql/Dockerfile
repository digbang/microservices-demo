FROM postgres:14-alpine

ONBUILD RUN psql -U ms-payments -d ms-payments -c "create extension unaccent;"
