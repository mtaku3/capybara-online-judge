FROM python:3.10.12-slim-bookworm

RUN apt-get update \
    && apt-get install --no-install-recommends -y time \
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /workspace

CMD ["tail", "-f", "/dev/null"]
