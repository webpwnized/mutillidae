# Stage 1: Build the application
FROM php:7.4-apache AS build
WORKDIR /var/www/html
COPY . /var/www/html/
RUN composer install --no-dev --prefer-dist

# Stage 2: Create the final image
FROM php:7.4-apache
RUN groupadd -r www-data && useradd -r -g www-data -G www-data -d /var/www/html www-data
RUN chown -R www-data:www-data /var/www/html
COPY --from=build /var/www/html/public /var/www/html/
COPY --from=build /var/www/html/.htaccess /var/www/html/
COPY --from=build /var/www/html/index.php /var/www/html/

# Install the required PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends libapache2-mod-php7.4 php7.4-mysql php7.4-curl

# Configure Apache to serve the Mutillidae-II application
RUN a2enmod rewrite
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf

# Expose the Apache port
EXPOSE 80

# Switch to the non-root user
USER www-data

# Start Apache when the container is launched
CMD ["apache2-foreground"]
