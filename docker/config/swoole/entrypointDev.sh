#!/bin/bash

# Obtém a data e hora atual no formato YYYY-MM-DD_HH-MM
current_datetime=$(date +"%Y-%m-%d_%H-%M")

# Define o nome do arquivo de log com base na data e hora
log_file="/var/log/php/docker_log_$current_datetime.txt"

echo "executing watch on python"

# Ativa o ambiente virtual e executa o script Python
source /opt/venv/bin/activate
cd /public/docker/config/swoole/ && python3 watch.py &

echo "install composer dependencies"
cd /public/
if composer install; then
    echo "start server"
    php server.php >> "$log_file" 2>&1
else
    echo "Failed to install composer dependencies" >&2
fi

echo "keep container running" && tail -f /dev/null