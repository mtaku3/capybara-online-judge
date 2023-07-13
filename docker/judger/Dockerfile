FROM php:8-bullseye as base

# Install PhpRedis

RUN pecl install redis && docker-php-ext-enable redis


# Configure php

ARG ENV

RUN apt-get update \
  && apt-get install -y libpq-dev \
  && docker-php-ext-install pgsql pdo pdo_pgsql


RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer


FROM base as development

COPY ./docker/judger/php/php.development.ini /usr/local/etc/php/php.ini

WORKDIR /workspace
ENTRYPOINT ["php", "src/Judger/Judge.php"]


FROM base as production

COPY ./docker/judger/php/php.production.ini /usr/local/etc/php/php.ini

WORKDIR /workspace
COPY ./ /workspace

RUN apt-get update && apt-get install -y git
RUN composer install

ENTRYPOINT ["php", "src/Judger/Judge.php"]
