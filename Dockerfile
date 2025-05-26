FROM python:3.12-slim AS pi-docs
WORKDIR /app

# build python deps
COPY docs/requirements.txt /app/
RUN pip install -r requirements.txt

COPY docs /app

RUN sphinx-build source _site

FROM node:21.6-slim AS pi-webpack
WORKDIR /app

RUN apt-get update \
    && apt-get install -y git \
    && npm upgrade -g npm \
    && npm upgrade -g yarn \
    && rm -rf /var/lib/apt/lists/*

# build js deps
COPY public/package.json public/yarn.lock public/webpack.config.js /app/
RUN yarn

# run webpack
COPY public /app
RUN yarn webpack


FROM pi-webpack AS pi-prod-assets

RUN yarn --production \
    && yarn cache clean


FROM php:7.4-apache AS pi
WORKDIR /var/www/html
ENV COMPOSER_ALLOW_SUPERUSER=1

# https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh required for symfony-cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libxslt1-dev \
        git \
        libmagickwand-dev \
        libzip-dev \
        zip  \
        unzip \
        ghostscript \
        libicu-dev \
        libapache2-mod-xsendfile \
        netcat-traditional \
        symfony-cli \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && a2enmod rewrite headers \
    && docker-php-ext-configure intl \
    && docker-php-ext-install xsl pdo pdo_mysql zip intl \
    && pecl install imagick pcov \
    && docker-php-ext-enable imagick pcov \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && git config --global --add safe.directory /var/www/html \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# default service settings
COPY docker/docker-entrypoint.sh /docker-entrypoint.sh
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/symfony.ini
COPY docker/image-policy.xml /etc/ImageMagick-6/policy.xml

CMD ["/docker-entrypoint.sh"]

# basic deps installer (no script/plugings)
COPY --chown=www-data:www-data --chmod=775 composer.json composer.lock /var/www/html/
RUN composer install --no-scripts

# copy project files and install all symfony deps
COPY --chown=www-data:www-data --chmod=775 . /var/www/html
# copy webpacked js and libs
COPY --chown=www-data:www-data --chmod=775 --from=pi-prod-assets /app/js/dist /var/www/html/public/js/dist
COPY --chown=www-data:www-data --chmod=775 --from=pi-prod-assets /app/css /var/www/html/public/css
COPY --chown=www-data:www-data --chmod=775 --from=pi-prod-assets /app/node_modules /var/www/html/public/node_modules
# copy docs
COPY --chown=www-data:www-data --chmod=775 --from=pi-docs /app/_site /var/www/html/public/docs/sphinx

RUN mkdir -p data/prod data/dev data/test var/cache/prod var/cache/dev var/cache/test var/sessions var/log \
    && chown -R www-data:www-data data var \
    && chmod -R 775 data var \
    && composer install \
    && ./bin/console cache:clear