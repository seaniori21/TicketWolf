# Use the official PHP image from Docker Hub
FROM php:8.1-apache

# Copy the local code to the container's web directory
COPY . /var/www/html/

# Install PHP extensions and dependencies
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Expose port 80 for the web server
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
