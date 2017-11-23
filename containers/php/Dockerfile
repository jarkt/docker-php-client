FROM ubuntu:16.04

ENV DEBIAN_FRONTEND noninteractive

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get -yq update \
    && apt-get install -yq \
        php7.0 \
        php7.0-curl \
        php7.0-xml \
        php7.0-mbstring \
        php7.0-zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /project

ENTRYPOINT ["/project/containers/php/entrypoint.sh"]
