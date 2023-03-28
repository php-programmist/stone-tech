#!/bin/bash

cd .docker

source .env
export VIRTUAL_HOST

# Run an console command
console () {
    docker compose exec php php bin/console "${@:1}" || docker compose run --rm php php bin/console "${@:1}"
}

# Build all of the images or the specified one
build () {
    docker compose build "${@:1}"
}

# Generate a new certificate
cert_generate () {
    rm -Rf etc/nginx/certs/*
    docker compose run --rm nginx sh -c "cd /etc/nginx/certs && touch openssl.cnf && cat /etc/ssl/openssl.cnf > openssl.cnf && echo \"\" >> openssl.cnf && echo \"[ SAN ]\" >> openssl.cnf && echo \"subjectAltName=DNS.1:${VIRTUAL_HOST},DNS.2:*.${VIRTUAL_HOST}\" >> openssl.cnf && openssl req -x509 -sha256 -nodes -newkey rsa:4096 -keyout ${VIRTUAL_HOST}.key -out ${VIRTUAL_HOST}.crt -days 3650 -subj \"/CN=*.${VIRTUAL_HOST}\" -config openssl.cnf -extensions SAN && rm openssl.cnf"
}

# Install the certificate
cert_install () {
    if [[ "$OSTYPE" == "darwin"* ]]; then
        sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain .docker/nginx/certs/demo.test.crt
    elif [[ "$OSTYPE" == "linux-gnu" ]]; then
        sudo ln -s "$(pwd)/.docker/nginx/certs/demo.test.crt" /usr/local/share/ca-certificates/demo.test.crt
        sudo update-ca-certificates
    else
        echo "Could not install the certificate on the host machine, please do it manually"
    fi
}

# Run a Composer command
composer () {
    docker compose exec php composer "${@:1}" || docker compose run --rm php composer "${@:1}"
}

# Stop and destroy all containers
down () {
    docker compose down "${@:1}"
}

# Create .env from .env.dist
env () {
    if [ ! -f .env ]; then
        cp .env.dist .env
        source .env
        export VIRTUAL_HOST
    fi
}

# Initialise the Docker environment and the application
init () {
    env \
        && down \
        && build

    if [ ! -f etc/nginx/certs/${VIRTUAL_HOST}.crt ]; then
        cert_generate
    fi

    start \
    && cert_install \
    && composer install \
    && console doctrine:migrations:migrate \
    && yarn install
}

# Display and tail the logs of all containers or the specified one's
logs () {
    docker compose logs -f "${@:1}"
}

# Restart the containers
restart () {
    stop && start
}

# Start the containers
start () {
    docker compose up -d  --remove-orphans
}

# Stop the containers
stop () {
    docker compose stop
}

# Update the Docker environment
update () {
    stop \
        && git pull \
        && build \
        && start \
        && composer install \
        && console doctrine:migrations:migrate \
        && yarn install
}

# Run a Yarn command
yarn () {
    docker compose exec nodejs yarn "${@:1}" || docker compose run --rm nodejs yarn "${@:1}"
}


#######################################
# MENU
#######################################

case "$1" in
    console)
        console "${@:2}"
        ;;
    build)
        build "${@:2}"
        ;;
    cert)
        case "$2" in
            generate)
                cert_generate
                ;;
            install)
                cert_install
                ;;
            *)
                cat << EOF
Управление сертификатами.
Использование:
    demo cert <command>
Возможные команды:
    generate .................................. Сгенерировать новые сертификаты
    install ................................... Установить сертификаты
EOF
                ;;
        esac
        ;;
    composer)
        composer "${@:2}"
        ;;
    down)
        down "${@:2}"
        ;;
    init)
        init
        ;;
    logs)
        logs "${@:2}"
        ;;
    restart)
        restart
        ;;
    start)
        start
        ;;
    stop)
        stop
        ;;
    update)
        update
        ;;
    yarn)
        yarn "${@:2}"
        ;;
    *)
        cat << EOF
Command line interface for the Docker-based web development environment demo.
Usage:
    demo <command> [options] [arguments]
Available commands:
    console ................................... Запускает консольные команды Symfony
    build [image] ............................. Сборка всех docker-образов или одного указанного
    cert ...................................... Управление сертификатами
        generate .............................. Сгенерировать новые сертификаты
        install ............................... Установить сертификаты
    composer .................................. Запускает команды Composer
    down ...................................... Останавливает и уничтожает все контейнеры
    init ...................................... Инициализирует Docker-окружение и само приложение
    logs [container] .......................... Отображает логи всех контейнеров или только указанного
    restart ................................... Перезапустить контейнеры
    start ..................................... Запустить контейнеры
    stop ...................................... Остановить контейнеры
    update .................................... Обновить Docker-окружение
    yarn ...................................... Запускает команды Yarn
EOF
        exit
        ;;
esac

cd -