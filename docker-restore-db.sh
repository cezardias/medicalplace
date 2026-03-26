#!/bin/bash

# Script para restaurar o banco de dados no contêiner Docker
# Uso: ./docker-restore-db.sh seu_arquivo.sql

IFILE=$1

if [ -z "$IFILE" ]; then
    echo "Uso: ./docker-restore-db.sh nome_do_arquivo.sql"
    exit 1
fi

if [ ! -f "$IFILE" ]; then
    echo "Erro: Arquivo $IFILE não encontrado."
    exit 1
fi

echo "Importando $IFILE para o banco de dados medicalplace_producao..."
docker exec -i mysql-server mysql -u root -pVktRkCKq4YwH medicalplace_producao < "$IFILE"

if [ $? -eq 0 ]; then
    echo "Sucesso! Banco de dados restaurado."
else
    echo "Erro durante a importação."
fi
