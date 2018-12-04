FROM php:7.2

RUN apt-get update && apt-get install -y git zlib1g-dev mysql-client
RUN docker-php-ext-install zip
RUN docker-php-ext-install pdo_mysql
RUN pecl install xdebug-2.6.0alpha1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=bin --filename composer
RUN php -r "unlink('composer-setup.php');"