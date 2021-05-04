FROM osixia/openldap

ENV LDAP_ORGANISATION="Mutillidae"
ENV LDAP_DOMAIN="mutillidae.local"

COPY mutillidae.ldif /container/service/slapd/assets/config/bootstrap/ldif/50-mutillidae.ldif
