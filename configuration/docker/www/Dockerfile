FROM php:7.4.18-apache

# Copy the TLS certificate files
COPY ./mutillidae/configuration/https-certificate/mutillidae-selfsigned.crt /etc/ssl/certs/mutillidae-selfsigned.crt
COPY ./mutillidae/configuration/https-certificate/mutillidae-selfsigned.key /etc/ssl/private/mutillidae-selfsigned.key

# Copy the Apache configuration
COPY ./mutillidae/configuration/apache-configuration/conf/ /etc/apache2/conf/
COPY ./mutillidae/configuration/apache-configuration/error-page/ /etc/apache2/error-pages
COPY ./mutillidae/configuration/apache-configuration/conf-available/aliases.conf /etc/apache2/conf-available/aliases.conf
COPY ./mutillidae/configuration/apache-configuration/conf/error-pages.conf /etc/apache2/conf/error-pages.conf
COPY ./mutillidae/configuration/apache-configuration/sites-available/mutillidae.conf /etc/apache2/sites-available/mutillidae.conf

# Copy the mutillidae project to Apache directory
COPY ./mutillidae /var/www/mutillidae
RUN sed -i 's/127.0.0.1/database/' /var/www/mutillidae/includes/database-config.inc

# Update packages
RUN apt-get update

# Install PHP requirements used by Mutillidae II
RUN docker-php-ext-install mysqli
RUN apt-get install -y libxml2-dev && docker-php-ext-install xml
RUN apt-get install -y libonig-dev && docker-php-ext-install mbstring
RUN apt-get install -y libcurl4-openssl-dev && docker-php-ext-install curl 

# Install nslookup to enable the command injection vulnerabilities
RUN apt-get install -y dnsutils

# Enable Apache modules and sites
RUN a2enmod ssl
RUN a2dissite 000-default
RUN a2ensite mutillidae

EXPOSE 80
EXPOSE 443