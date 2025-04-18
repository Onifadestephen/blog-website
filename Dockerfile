# Use the official PHP image from Docker Hub
FROM php:8.1-apache

# Enable mod_rewrite for Apache (if needed for your PHP app)
RUN a2enmod rewrite

# Install PostgreSQL extensions for PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the entire PHP project into the container's working directory
COPY . /var/www/html

# Expose the default port for Apache (80)
EXPOSE 80
