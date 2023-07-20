FROM php:8-bullseye as base

ARG ENV

# Run apt-get update first
RUN apt-get update

# Install extensions of php
RUN apt-get install --no-install-recommends -y libpq-dev \
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

# Clean up apt
RUN apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

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
RUN apt-get install --no-install-recommends -y git
RUN composer install --no-dev

# Clean up apt
RUN apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["php", "src/Judger/Judge.php"]
