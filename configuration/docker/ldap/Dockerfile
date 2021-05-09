# mutillidae/ldap:latest
FROM osixia/openldap

ENV LDAP_ORGANISATION="Mutillidae"
ENV LDAP_DOMAIN="mutillidae.local"
ENV LDAP_ADMIN_PASSWORD="mutillidae"
ENV LDAP_BASE_DN="dc=mutillidae,dc=local"
ENV LDAP_TLS="false"

COPY mutillidae.ldif /container/service/slapd/assets/config/bootstrap/ldif/50-mutillidae.ldif