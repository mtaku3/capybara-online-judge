FROM php:8-bullseye

# Install PhpRedis

RUN pecl install redis && docker-php-ext-enable redis


# Configure php

ARG ENV

COPY ./docker/apache/php/php.development.ini /
COPY ./docker/apache/php/php.production.ini /
RUN if [ "$ENV" = "production" ]; then cp /php.production.ini /usr/local/etc/php/php.ini; else cp /php.development.ini /usr/local/etc/php/php.ini; fi

RUN apt-get update \
  && apt-get install -y libpq-dev \
  && docker-php-ext-install pgsql pdo pdo_pgsql


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer


COPY ./docker/judger/entrypoint.sh /
RUN chmod +x /entrypoint.sh


WORKDIR /workspace

ENTRYPOINT /entrypoint.sh
