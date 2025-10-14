FROM php:7.4-apache

# 필요한 PHP 확장 설치
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    inotify-tools \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && docker-php-ext-enable mysqli

# Composer 설치 (CodeIgniter 의존성 관리용)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Apache 모듈 활성화
RUN a2enmod rewrite
RUN a2enmod headers

# 작업 디렉토리 설정
WORKDIR /var/www/html

# Apache 설정 최적화
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 개발 환경을 위한 Apache 설정
RUN echo "<Directory /var/www/html>" >> /etc/apache2/apache2.conf
RUN echo "    Options Indexes FollowSymLinks" >> /etc/apache2/apache2.conf
RUN echo "    AllowOverride All" >> /etc/apache2/apache2.conf
RUN echo "    Require all granted" >> /etc/apache2/apache2.conf
RUN echo "</Directory>" >> /etc/apache2/apache2.conf

# PHP 설정 최적화 (개발 환경)
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/development.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/development.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/development.ini
RUN echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/development.ini

# 권한 설정 (개발 환경용)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# 로그 디렉토리 생성
RUN mkdir -p /var/log/apache2
RUN chown -R www-data:www-data /var/log/apache2

EXPOSE 80
