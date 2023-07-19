FROM php:8-bullseye as base

ARG ENV

# Install extensions of php
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install composer
RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer


FROM base as development

# Configure apache2
COPY ./docker/judger/php/php.development.ini /usr/local/etc/php/php.ini

WORKDIR /workspace
ENTRYPOINT ["php", "src/Judger/Judge.php"]


FROM base as production

# Manually set ENV to production
ENV ENV production

# Configure apache2
COPY ./docker/judger/php/php.production.ini /usr/local/etc/php/php.ini

# Copy source files
WORKDIR /workspace
COPY ./ /workspace

# Install php dependencies
RUN apt-get update && apt-get install -y git
RUN composer install --no-dev

ENTRYPOINT ["php", "src/Judger/Judge.php"]
