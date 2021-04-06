FROM debian:buster-slim

# Allow apt to complete without user prompts
ENV DEBIAN_FRONTEND=noninteractive

# update the system
RUN apt update -y
RUN apt full-upgrade -y

# add packages
RUN apt install -y apache2 php php-mysql php-xml php-mbstring php-curl

# copy source
WORKDIR /var/www/mutillidae
COPY . .

# apache config
COPY configuration/apache-configuration/sites-available/mutillidae.conf /etc/apache2/sites-available/mutillidae.conf
COPY configuration/apache-configuration/conf/error-pages.conf /etc/apache2/conf/error-pages.conf
COPY configuration/https-certificate/mutillidae-selfsigned.crt /etc/ssl/certs/mutillidae-selfsigned.crt
COPY configuration/https-certificate/mutillidae-selfsigned.key /etc/ssl/private/mutillidae-selfsigned.key
RUN sed -i 's/VirtualHost 127.0.0.1:80/VirtualHost *:80/' /etc/apache2/sites-available/mutillidae.conf
RUN sed -i 's/VirtualHost 127.0.0.1:443/VirtualHost *:443/' /etc/apache2/sites-available/mutillidae.conf
RUN a2enmod ssl
RUN a2dissite 000-default
RUN a2ensite mutillidae

# Mutillidae restricts access to localhost and 192.168.0.0/16 by default via .htaccess file.
# Set this to allow from all and manage restrictions in the docker-compose config.
RUN sed -i 's/Deny from all/Allow from all/' .htaccess

# Update mutillidae database config to use the mysql docker instance
RUN sed -i 's/127.0.0.1/db/' includes/database-config.inc

# run apache in foreground
CMD ["apachectl", "-D", "FOREGROUND"]

# expose ports
EXPOSE 80
EXPOSE 443
