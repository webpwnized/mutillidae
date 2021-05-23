# mutillidae/ldap:latest

FROM osixia/openldap

ENV LDAP_ORGANISATION="Mutillidae"
ENV LDAP_DOMAIN="mutillidae.local"
ENV LDAP_ADMIN_PASSWORD="mutillidae"
ENV LDAP_BASE_DN="dc=mutillidae,dc=local"
ENV LDAP_TLS="false"

# Update software packages
RUN apt-get update

# Install wget
RUN apt-get install -y wget

# Download the Mutillidae LDIF file from GitHub
RUN wget -O /container/service/slapd/assets/config/bootstrap/ldif/50-mutillidae.ldif https://raw.githubusercontent.com/webpwnized/mutillidae/master/configuration/openldap/mutillidae.ldif

# Uninstall wget
RUN apt-get remove -y wget