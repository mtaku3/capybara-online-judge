FROM mcr.microsoft.com/devcontainers/php:8

COPY ./apache2.conf /etc/apache2/
COPY ./000-default.conf /etc/apache2/sites-available/

RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

COPY ./entrypoint.sh /

WORKDIR /workspace

ENTRYPOINT /entrypoint.sh
