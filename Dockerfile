FROM php:7.3.33-fpm-alpine

ENV TZ=Asia/Yekaterinburg

WORKDIR /var/www/html

RUN set -ex; \
    apk --no-cache update && \
    apk --no-cache add mc git tzdata \
    ;


#COPY . .

RUN set -ex; \
    git clone https://github.com/demonwork/escada-config.git /tmpdev && \
    git --bare init /git && \
    cd /tmpdev && \
    git push /git \
    ; \
    export PHP_DEPS="curl-dev icu-dev" \
    ; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    ; \
    apk add --no-cache --virtual .php-deps $PHP_DEPS \
    ; \
    pecl install raphf-2.0.1 && \
    echo extension=raphf.so > /usr/local/etc/php/conf.d/01-raphf.ini && \
    pecl install propro-2.1.0 && \
    echo extension=propro.so > /usr/local/etc/php/conf.d/02-propro.ini && \
    pecl install pecl_http-3.2.4 && \
    echo extension=http.so > /usr/local/etc/php/conf.d/03-http.ini && \
    docker-php-ext-install pcntl bcmath curl sockets mysqli pdo_mysql \
    ; \
    apk del --no-network .build-deps \
    ; \
    apk del --no-network .php-deps \
    ; \
    apk add curl icu ffmpeg && \
    ln -s /usr/bin/ffmpeg /usr/bin/avconv && \
    ./composer install && \
    tar -czvf /vendor.tar.gz vendor && \
    rm -rf /tmpdev \
    ;

COPY docker-yii2-entrypoint docker-worker-entrypoint /usr/local/bin/

#RUN set -ex; \
#    ./composer install \
#    ;




#FROM ubuntu:18.04 as ubuntu
#WORKDIR /opt/escada
#RUN mkdir logs config
#COPY --from=builder /app/build/escada_core /opt/escada
#COPY --from=builder /app/config/escada.conf.dist /opt/escada/config/escada.conf
#USER root
#RUN set -ex; \
#    apt update && \
#    apt install -y libnettle6 libmysqlclient20 libgtop-2.0 libuuid1 libjsoncpp1 tzdata \
#    ;

#CMD ["docker-yii2-entrypoint"]

