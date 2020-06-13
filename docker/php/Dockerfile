FROM php:7.4-fpm

WORKDIR /var/www/html
ADD . /var/www/html

RUN DEBIAN_FRONTEND=noninteractive adduser user
RUN DEBIAN_FRONTEND=noninteractive chown -R user:user /var/www/html
RUN DEBIAN_FRONTEND=noninteractive apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install -y \
  locales \
  libicu-dev \
  libxml2-dev \
  libxml2-dev \
  libfontconfig \
  bzip2 zlib1g-dev \
  libcurl4-gnutls-dev \
  libtidy-dev \
  openssh-server libonig-dev libzip-dev \
  && pecl channel-update pecl.php.net \
  && docker-php-ext-install intl soap mbstring xml mysqli pdo_mysql opcache zip curl \
  && sed -i 's/pm.max_children = 5/pm.max_children = 25/' /usr/local/etc/php-fpm.d/www.conf \
  && echo 'php_admin_value[memory_limit] = 256M' >> /usr/local/etc/php-fpm.d/www.conf \
  && echo 'php_admin_value[error_log] = /proc/self/fd/2' >> /usr/local/etc/php-fpm.d/www.conf \
  && echo 'php_admin_flag[log_errors] = on' >> /usr/local/etc/php-fpm.d/www.conf \
  && echo 'auto_detect_line_endings=1' > /usr/local/etc/php/conf.d/30-line-endings.ini \
  && apt-get clean -y \
  && rm -rf /var/lib/apt/lists/* \
  && echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen --purge \
  && pecl install -o -f redis \
  && rm -rf /tmp/pear \
  && docker-php-ext-enable redis \
  && mkdir -p /etc/nginx/conf.d

RUN docker-php-ext-install pcntl bcmath

RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

COPY ./xdebug.ini /usr/local/etc/php/conf.d/

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# Define PHP_TIMEZONE env variable
ENV PHP_TIMEZONE UTC

# Add www-data to root group and viceversa
RUN usermod -a -G user root
RUN usermod -a -G root user

ENV LANG="en_US.UTF-8" LANGUAGE="en_US:en" LC_ALL="en_US.UTF-8" PHP_TIMEZONE="UTC"

