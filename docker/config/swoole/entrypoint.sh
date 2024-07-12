#!/bin/sh


# Obtém a data e hora atual no formato YYYY-MM-DD_HH-MM
current_datetime=$(date +"%Y-%m-%d_%H-%M")

# Define o nome do arquivo de log com base na data e hora
log_file="/var/log/php/docker_log_$current_datetime.txt"


echo "install composer dependencies"
cd /public/ && composer install && echo "start server" && php server.php

