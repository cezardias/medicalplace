FROM ubuntu:20.04
RUN apt-get update -y
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y wget ca-certificates apt-transport-https software-properties-common iputils-ping vim curl
RUN DEBIAN_FRONTEND=noninteractive add-apt-repository -y ppa:ondrej/php
RUN apt-get update -y
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y apache2 php7.4 php7.4-curl php7.4-pdo php7.4-mbstring php7.4-iconv php7.4-tokenizer php7.4-pcov php7.4-dom php7.4-zip php7.4-mysql libapache2-mod-php7.4 supervisor

# Configure Apache
RUN a2enmod rewrite
COPY docker-apache.conf /etc/apache2/sites-available/000-default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN wget https://getcomposer.org/download/2.0.12/composer.phar
RUN mv composer.phar /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer

RUN wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash
RUN bash -i -c 'nvm install v14.16.1'

RUN mkdir -p /var/www/storage/framework/{sessions,views,cache}
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

WORKDIR /var/www/
COPY ./ /var/www/
# .env should be managed via volumes or manual copy on the host

RUN composer install --no-interaction --no-dev --optimize-autoloader
RUN bash -i -c 'npm install && npm run prod'

RUN chown -R www-data:www-data /var/www

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]