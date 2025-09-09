# Use official PHP image with Apache
FROM php:8.2-cli

# Install PDO_PGSQL extension
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app

# Expose port 10000
EXPOSE 10000

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
