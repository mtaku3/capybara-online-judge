FROM mcr.microsoft.com/devcontainers/php:8

COPY ./apache2.conf /etc/apache2/
COPY ./000-default.conf /etc/apache2/sites-available/

ENTRYPOINT [ "apache2ctl", "-D", "FOREGROUND" ]
