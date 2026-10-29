FROM privatebin/nginx-fpm-alpine:2.1.0@sha256:0f80fb240c3c3073c53a82893da7c7d62f03f4e05403855b720e4371541ba412

# Narrow backports pending PrivateBin 2.1.1; do not deploy a moving branch.
USER root
COPY deploy/patches/ /tmp/pursuit-patches/
RUN apk add --no-cache patch \
    && patch --batch --fuzz=0 -p1 -d /srv -i /tmp/pursuit-patches/request-size.patch \
    && patch --batch --fuzz=0 -p1 -d /var/www -i /tmp/pursuit-patches/optional-controls.patch \
    && rm -rf /tmp/pursuit-patches \
    && apk del patch

LABEL org.opencontainers.image.title="Pursuit Secrets" \
      org.opencontainers.image.description="Pursuit-themed PrivateBin deployment" \
      org.opencontainers.image.source="https://github.com/Pursuit-Assets/pursuit-secrets" \
      org.opencontainers.image.version="2.1.0-pursuit.1"

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

# Fail the build if a future base-image change loses the PHP security floor.
RUN php -r 'exit(version_compare(PHP_VERSION, "8.5.12", ">=") && (!extension_loaded("session") || in_array(ini_get("session.upload_progress.enabled"), ["0", ""], true)) ? 0 : 1);' \
    && php -l /srv/lib/Request.php \
    && php -l /srv/lib/Controller.php \
    && php -l /srv/lib/FormatV2.php \
    && php -l /srv/tpl/bootstrap5.php \
    && php-fpm85 --test
