FROM mcr.microsoft.com/devcontainers/php:8-bullseye AS base

ARG ENV

# Configure common configurations of apache2
COPY ./docker/apache/apache2.conf /etc/apache2/
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

# Remove default site configurations
RUN rm -rf /etc/apache2/sites-enabled && mkdir /etc/apache2/sites-enabled

# Install extensions of php
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Configure ownership of data directory to save all files uploaded to COJ
WORKDIR /data
RUN chown -R www-data:www-data /data


FROM base AS development

# Configure apache2
COPY ./docker/apache/000-default.conf /etc/apache2/sites-enabled/

# Configure php
COPY ./docker/apache/php/php.development.ini /usr/local/etc/php/php.ini

# Install node and npm
ENV NODE_VERSION 20.0.0
RUN ARCH= && dpkgArch="$(dpkg --print-architecture)" \
    && case "${dpkgArch##*-}" in \
    amd64) ARCH='x64';; \
    ppc64el) ARCH='ppc64le';; \
    s390x) ARCH='s390x';; \
    arm64) ARCH='arm64';; \
    armhf) ARCH='armv7l';; \
    i386) ARCH='x86';; \
    *) echo "unsupported architecture"; exit 1 ;; \
    esac \
    # gpg keys listed at https://github.com/nodejs/node#release-keys
    && set -ex \
    && for key in \
    4ED778F539E3634C779C87C6D7062848A1AB005C \
    141F07595B7B3FFE74309A937405533BE57C7D57 \
    74F12602B6F1C4E913FAA37AD3A89613643B6201 \
    DD792F5973C6DE52C432CBDAC77ABFA00DDBF2B7 \
    61FC681DFB92A079F1685E77973F295594EC4689 \
    8FCCA13FEF1D0C2E91008E09770F7A9A5AE15600 \
    C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
    890C08DB8579162FEE0DF9DB8BEAB4DFCF555EF4 \
    C82FA3AE1CBEDC6BE46B9360C43CEC45C17AB93C \
    108F52B48DB57BB0CC439B2997B01419BD92F80A \
    ; do \
    gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys "$key" || \
    gpg --batch --keyserver keyserver.ubuntu.com --recv-keys "$key" ; \
    done \
    && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-$ARCH.tar.xz" \
    && curl -fsSLO --compressed "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
    && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
    && grep " node-v$NODE_VERSION-linux-$ARCH.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
    && tar -xJf "node-v$NODE_VERSION-linux-$ARCH.tar.xz" -C /usr/local --strip-components=1 --no-same-owner \
    && rm "node-v$NODE_VERSION-linux-$ARCH.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
    && ln -s /usr/local/bin/node /usr/local/bin/nodejs \
    # smoke tests
    && node --version \
    && npm --version

# Install dependencies of tools
RUN apt-get update \
  && apt-get install -y openjdk-11-jre-headless graphviz

WORKDIR /workspace
ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]


FROM node:20.0.0-bullseye AS builder

# Copy source files
WORKDIR /workspace
COPY ./ /workspace/

# npm install and build
RUN npm install \
    && npx tailwindcss -i ./src/App/input.css -o ./dist/tailwind.css

# Copy dependencies
RUN cp ./node_modules/flowbite/dist/flowbite.min.js ./dist/ \
    && cp ./node_modules/filepond/dist/filepond.min.js ./dist/ \
    && cp ./node_modules/filepond/dist/filepond.min.css ./dist/ \
    && cp ./node_modules/filepond-plugin-tarball/bundled/tarball.min.js ./dist/

# Remove unnecessary files
RUN rm -rf ./node_modules


FROM base AS production

# Manually set ENV to production
ENV ENV production

# Configure Apache2
RUN ln -s /etc/apache2/mods-available/ssl.load /etc/apache2/mods-enabled/
RUN ln -s /etc/apache2/mods-available/ssl.conf /etc/apache2/mods-enabled/
RUN ln -s /etc/apache2/mods-available/socache_shmcb.load /etc/apache2/mods-enabled/
COPY ./docker/apache/000-default-ssl.conf /etc/apache2/sites-enabled/

# Configure php
COPY ./docker/apache/php/php.production.ini /usr/local/etc/php/php.ini

# Copy source files from builder
WORKDIR /workspace
COPY --from=builder /workspace /workspace
RUN chown -R www-data:www-data /workspace

# Install php dependencies
RUN apt-get update && apt-get install -y git
RUN composer install --no-dev

ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]
