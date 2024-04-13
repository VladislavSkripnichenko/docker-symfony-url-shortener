FROM php:8.1-fpm

# Update and install dependencies
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# Set the working directory
WORKDIR /var/www/project

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Attempt to install Symfony CLI and capture any errors
RUN curl -sS https://get.symfony.com/cli/installer | bash > symfony_installer.log 2>&1 || (cat symfony_installer.log && exit 1)
RUN ls -la /root/  # Check contents of the root directory to find Symfony CLI

# Check and move Symfony binary if present
RUN if [ -f /root/.symfony5/bin/symfony ]; then \
        mv /root/.symfony5/bin/symfony /usr/local/bin/symfony; \
    else \
        echo "Symfony binary not found in expected locations, checking alternative paths..."; \
        ls -la /root/; \
        exit 1; \
    fi
