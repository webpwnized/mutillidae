FROM debian:stretch

RUN apt update && apt install -y \
      apache2 libapache2-mod-php \
      php php-mysql php-curl php-mbstring php-xml \
      mysql-server && \
    rm -rf /var/lib/apt/lists/* && \
    a2enmod rewrite && \
    VAR_WWW_LINE=$(grep -n '<Directory /var/www/>' /etc/apache2/apache2.conf | cut -f1 -d:) && \
    VAR_WWW_END_LINE=$(tail -n +$VAR_WWW_LINE /etc/apache2/apache2.conf | grep -n '</Directory>' | head -n 1 | cut -f1 -d:) && \
    REPLACE_ALLOW_OVERRIDE_LINE=$(($(tail -n +$VAR_WWW_LINE /etc/apache2/apache2.conf | head -n "$VAR_WWW_END_LINE" | grep -n AllowOverride | cut -f1 -d:) + $VAR_WWW_LINE - 1)) && \
    sed -i "${REPLACE_ALLOW_OVERRIDE_LINE}s/None/All/" /etc/apache2/apache2.conf && \
    service mysql start && \
    while [ ! -S /var/run/mysqld/mysqld.sock ]; do sleep 1; done && \
    sleep 5 && \
    echo "update user set authentication_string=PASSWORD('mutillidae') where user='root';" | mysql -u root -v mysql && \
    echo "update user set plugin='mysql_native_password' where user='root';" | mysql -u root -v mysql && \
    service mysql stop && \
    sed -i 's/^error_reporting.*/error_reporting = E_ALL/g' /etc/php/7.0/apache2/php.ini && \
    sed -i 's/^display_errors.*/display_errors = On/g' /etc/php/7.0/apache2/php.ini

ADD . /var/www/html/mutillidae

RUN sed -i 's/^Deny from all/Allow from all/g' /var/www/html/mutillidae/.htaccess

EXPOSE 80 443

CMD ["bash", "-c", "service mysql start && service apache2 start && sleep infinity & wait"]