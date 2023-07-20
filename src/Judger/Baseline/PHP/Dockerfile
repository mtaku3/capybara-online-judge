FROM php:8.2.8-bookworm

RUN apt-get update \
    && apt-get install -y time

WORKDIR /workspace

CMD ["tail", "-f", "/dev/null"]
