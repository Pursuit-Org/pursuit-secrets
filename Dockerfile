FROM privatebin/nginx-fpm-alpine:2.0.5@sha256:91e3adf17da1c9e2715cb57ff22940a6644252a5488d9819a64eebbaf64cc58a

LABEL org.opencontainers.image.title="Pursuit Secrets" \
      org.opencontainers.image.description="Pursuit-themed PrivateBin deployment" \
      org.opencontainers.image.source="https://github.com/Pursuit-Org/pursuit-secrets" \
      org.opencontainers.image.version="2.0.5-pursuit.1"

COPY --chown=65534:82 cfg/conf.php /srv/cfg/conf.php
COPY --chown=65534:82 tpl/bootstrap5.php /srv/tpl/bootstrap5.php
COPY --chown=65534:82 css/pursuit.css /var/www/css/pursuit.css
COPY --chown=65534:82 js/pursuit.js /var/www/js/pursuit.js
COPY --chown=65534:82 img/pursuit-mark.svg /var/www/img/pursuit-mark.svg
COPY --chown=65534:82 img/pursuit-profile-purple.png /var/www/img/pursuit-profile-purple.png
COPY --chown=65534:82 fonts /var/www/fonts
COPY --chown=65534:82 deploy/nginx/hsts.conf /etc/nginx/location.d/hsts.conf
COPY deploy/nginx/uploads.conf /etc/nginx/server.d/uploads.conf
COPY deploy/php/uploads.ini /etc/php/conf.d/99-uploads.ini

USER 65534:82
