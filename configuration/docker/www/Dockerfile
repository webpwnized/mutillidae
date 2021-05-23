# Build mutillidae/www:latest

FROM php:8.0.6-apache

# Update software packages
RUN apt-get update

# Install git
RUN apt-get install -y git

# Copy the mutillidae project to Apache web files directory
RUN git clone https://github.com/webpwnized/mutillidae.git /var/www/mutillidae

# uninstall git
RUN apt-get remove -y git

# Change the hostname of the database server to the docker database hostname
RUN sed -i 's/127.0.0.1/database/' /var/www/mutillidae/includes/database-config.inc

# Change the hostname of the ldap server to the docker ldap hostname
RUN sed -i 's/127.0.0.1/ldap/' /var/www/mutillidae/includes/ldap-config.inc

# Copy the TLS certificate files from mutillidae into cert directories
RUN cp /var/www/mutillidae/configuration/https-certificate/mutillidae-selfsigned.crt /etc/ssl/certs/mutillidae-selfsigned.crt
RUN cp /var/www/mutillidae/configuration/https-certificate/mutillidae-selfsigned.key /etc/ssl/private/mutillidae-selfsigned.key

# Copy the Apache configuration from Mutillidae into Apache conf
RUN mkdir /etc/apache2/conf/
RUN cp /var/www/mutillidae/configuration/apache-configuration/conf/error-pages.conf /etc/apache2/conf/error-pages.conf
RUN cp /var/www/mutillidae/configuration/apache-configuration/conf/headers.conf /etc/apache2/conf/headers.conf

RUN mkdir /var/www/mutillidae/error-pages/
RUN cp /var/www/mutillidae/configuration/apache-configuration/error-pages/404.html /var/www/mutillidae/error-pages/404.html
RUN cp /var/www/mutillidae/configuration/apache-configuration/error-pages/oops.jpg /var/www/mutillidae/error-pages/oops.jpg

RUN cp /var/www/mutillidae/configuration/apache-configuration/conf-available/aliases.conf /etc/apache2/conf-available/aliases.conf

RUN cp /var/www/mutillidae/configuration/apache-configuration/sites-available/mutillidae.conf /etc/apache2/sites-available/mutillidae.conf

# Install PHP requirements used by Mutillidae II
RUN apt-get install -y libldap2-dev && docker-php-ext-install ldap
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