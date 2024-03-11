# Use PHP 8.2 as the base image
FROM php:8.2-cli

# Install dependencies and extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libexif-dev \
    libonig-dev \
    unzip \
    git \
    curl

# Install the PHP extensions
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath zip

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/

# Copy existing application directory
COPY ./www /var/www

# Set the working directory to the Laravel app
WORKDIR /var/www/my-app

# Expose the port that the app is running on
EXPOSE 80

# Start the app using PHP’s built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
