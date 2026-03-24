FROM php:8.2-fpm

# Set frontend to noninteractive to prevent debconf issues
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm # Added nodejs and npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy package.json and package-lock.json for caching npm install
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm install

# Copy the rest of the application files
COPY . .

# Compile frontend assets
RUN npm run production

# Set ownership for the application directory
RUN chown -R www-data:www-data /var/www

# Change current user to www
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
