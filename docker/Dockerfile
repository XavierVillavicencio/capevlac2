# Use an official PHP runtime
FROM php:8.1-apache
# Enable Apache modules
RUN a2enmod ssl && a2enmod rewrite
RUN a2ensite default-ssl
RUN mkdir -p /etc/apache2/ssl
RUN echo 'max_execution_time = 6666666' >> /usr/local/etc/php/conf.d/docker-php-maxexectime.ini;
RUN echo 'upload_max_filesize = 10G' >> /usr/local/etc/php/conf.d/docker-php-upload_max_filesize.ini;
RUN echo 'post_max_size = 10G' >> /usr/local/etc/php/conf.d/docker-php-post_max_size.ini;

# INSTALL DEPENDENCIES
RUN apt-get update
RUN apt-get install -y build-essential 
# RUN apt-get install -y libpng-dev
# RUN apt-get install -y libfreetype6-dev 
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libldb-dev
RUN apt-get install -y libldap2-dev 
RUN apt-get install -y zip
RUN apt-get install -y unzip
RUN apt-get install -y libzip-dev
# RUN apt-get install -y libmagickwand-dev
RUN apt-get install -y default-mysql-client 
RUN apt-get install -y openssl
RUN apt-get install -y curl
RUN apt-get install -y libcurl4
RUN apt-get install -y libcurl4-openssl-dev
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y libicu-dev
RUN ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
&& ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so

# memcached
# RUN pecl install memcached-3.1.5
# RUN docker-php-ext-enable memcached

# mcrypt
# RUN pecl install mcrypt-1.0.3
# RUN docker-php-ext-enable mcrypt

# configure, install and enable all php packages
# RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd
RUN docker-php-ext-configure mysqli --with-mysqli=mysqlnd
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure zip

RUN docker-php-ext-install -j$(nproc) pdo_mysql
RUN docker-php-ext-install -j$(nproc) mysqli
RUN docker-php-ext-install -j$(nproc) pdo
# RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) intl
RUN docker-php-ext-install -j$(nproc) zip


# install imagick
# RUN pecl install imagick
# RUN docker-php-ext-enable imagick

# install composer
RUN cd /tmp \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install any extensions you need
RUN docker-php-ext-install curl exif fileinfo intl   

# Set the working directory to /var/www/html

WORKDIR /

# INSTALL SSL CERTIFICATES

COPY c94bc8c035cbaa1.crt /etc/apache2/ssl/
COPY olade.key /etc/apache2/ssl/
COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

WORKDIR /var/www/html

# Copy the source code in /www into the container at /var/www/html
#COPY ../. .
RUN chown -R www-data /var/www/html/
RUN chmod -R 777 /var/www/html/
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
# RUN install-php-extensions gd openssl
RUN install-php-extensions openssl
# clean image
RUN apt-get clean

RUN chown -R www-data /var/www/html/
RUN chmod -R 777 /var/www/html/
# da poniendo el htaccess para las url mod rewritte
COPY htaccess /var/www/html/.htaccess

EXPOSE 80
EXPOSE 443
