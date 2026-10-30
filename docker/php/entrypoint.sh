#!/bin/bash
# Завершать скрипт при любой ошибке
set -e

# Готовим ENV-переменные для cron-а по белому списку
# и запускаем cron.
printenv | grep -E "^(APPLICATION_ENV|CONFIG_CIPHER)" > /etc/environment
cron &

# Вызываем оригинальный entrypoint образа PHP, если переданы аргументы
if [ $# -eq 0 ]; then
    exec docker-php-entrypoint php-fpm
else
    exec docker-php-entrypoint "$@"
fi
