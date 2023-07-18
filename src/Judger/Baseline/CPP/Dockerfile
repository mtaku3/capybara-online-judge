FROM gcc:13.1.0-bookworm

RUN apt-get update \
    && apt-get install -y time

WORKDIR /workspace

CMD ["tail", "-f", "/dev/null"]
