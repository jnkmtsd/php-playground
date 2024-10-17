FROM php:8.2-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /app
RUN apt-get update && apt-get install -y \
    unzip \
 && apt-get clean \
 && rm -rf /var/lib/apt/lists/*
