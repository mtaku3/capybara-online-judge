FROM python:3.10.12-slim-bookworm

RUN apt-get update \
    && apt-get install -y time

WORKDIR /workspace

CMD ["tail", "-f", "/dev/null"]
