FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies (suppress warnings)
RUN composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true

# Create uploads directory and set permissions
RUN mkdir -p uploads/product_images && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 uploads

# Enable Apache modules
RUN a2enmod rewrite

# Create Apache virtual host configuration
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    ServerName localhost\n\
    \n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php index.html\n\
    </Directory>\n\
    \n\
    # Handle PHP files\n\
    <FilesMatch \.php$>\n\
        SetHandler application/x-httpd-php\n\
    </FilesMatch>\n\
    \n\
    # Error and access logs\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set environment variables for PHP
ENV APACHE_DOCUMENT_ROOT=/var/www/html
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
