FROM phpswoole/swoole:5.1.3-php8.3

# copy entrypoint to container to /home
COPY ./docker/config/swoole/entrypoint.sh /home/entrypoint.sh
COPY ./docker/config/swoole/entrypointDev.sh /home/entrypointDev.sh
# make entrypoint executable
RUN chmod +x /home/entrypoint.sh
RUN chmod +x /home/entrypointDev.sh

WORKDIR /public

RUN apt-get update && apt-get install vim -y && \
    apt-get install openssl -y && \
    apt-get install libssl-dev -y && \
    apt-get install wget -y && \
    apt-get install git -y && \
    apt-get install procps -y && \
    apt-get install htop -y && \
    apt-get install redis -y && \
    apt-get install python3-pip python3-venv -y

COPY ./docker/config/swoole/requirements.txt /tmp/requirements.txt

#Configurar o ambiente virtual Python e instalar dependências


RUN python3 -m venv /opt/venv && \
    /opt/venv/bin/pip install --upgrade pip && \
    /opt/venv/bin/pip install -r /tmp/requirements.txt && \
    . /opt/venv/bin/activate

# Atualizaçes e instalaçes de extenses PHP
RUN set -ex \
    && apt update && apt upgrade --yes \
    && apt install --yes libzip-dev \
    && pecl update-channels \
    && pecl install inotify \
    && docker-php-ext-enable inotify \
    && apt clean && rm -rf /var/lib/apt/lists && rm -rf /tmp/pear

RUN if ! php -m | grep -q 'redis'; then \
        pecl install -o -f redis && \
        docker-php-ext-enable redis; \
    fi

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_mysql
# Configuração adicional para a extensão GD com suporte a JPEG, PNG e WebP
RUN apt-get update && \
    apt-get install -y libjpeg62-turbo-dev libpng-dev libfreetype6-dev libwebp-dev && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/ --with-webp=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd

RUN apt install -y curl

#gzip
RUN apt-get install -y libz-dev && \
docker-php-ext-install zip

RUN apt-get install libsodium-dev -y
RUN docker-php-ext-install sodium

# Configurar o fuso horário
RUN ln -sf /usr/share/zoneinfo/America/Fortaleza /etc/localtime

#memory limit
RUN echo "memory_limit = 650M" > /usr/local/etc/php/conf.d/memory-limit.ini


# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-install pdo_pgsql

#enable opcache
RUN docker-php-ext-enable opcache
# Configuração JIT para melhorar o desempenho

# Configuração OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.enable_file_override=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Desabilitar JIT para evitar conflitos com Swoole
RUN echo "opcache.jit=0" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.jit_buffer_size=0" >> /usr/local/etc/php/conf.d/opcache.ini
#install pcov
RUN pecl install pcov
RUN docker-php-ext-enable pcov

ENTRYPOINT [ "/home/entrypointDev.sh" ]
