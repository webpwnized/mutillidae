# OWASP Mutillidae II - Web Container
# WARNING: This is a DELIBERATELY VULNERABLE application for security training only
# DO NOT expose to the internet or run on production systems

FROM php:8.2-apache

LABEL maintainer="OWASP Mutillidae Project"
LABEL description="OWASP Mutillidae II - Deliberately Vulnerable Web Application"

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libssl-dev \
    iputils-ping \
    dnsutils \
    curl \
    traceroute \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
# Detect architecture for LDAP library path
RUN if [ "$(uname -m)" = "aarch64" ]; then \
        LIBDIR="lib/aarch64-linux-gnu"; \
    else \
        LIBDIR="lib/x86_64-linux-gnu"; \
    fi && \
    docker-php-ext-configure ldap --with-libdir=${LIBDIR} && \
    docker-php-ext-install -j$(nproc) \
        mysqli \
        curl \
        xml \
        dom \
        ldap \
        simplexml

# Enable Apache modules
RUN a2enmod rewrite ssl headers

# Copy custom PHP configuration for vulnerability demonstrations
COPY php.ini /usr/local/etc/php/conf.d/mutillidae.ini

# Set working directory
WORKDIR /var/www/html

# Apache configuration - allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Expose HTTP port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

CMD ["apache2-foreground"]
