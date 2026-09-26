FROM php:8.4-fpm

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
    libfreetype6-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Błędy PHP tylko do logu, nie na stronę (obraz nie ma produkcyjnego php.ini)
COPY docker/php/zz-crm.ini /usr/local/etc/php/conf.d/zz-crm.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node 24 LTS z oficjalnego obrazu. Debian daje Node 20, bez wsparcia od 04.2026.
COPY --from=node:24-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:24-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Set working directory
WORKDIR /var/www

# Copy package.json and package-lock.json for caching npm install
COPY package.json package-lock.json ./

# Install Node.js dependencies (dokładnie wg package-lock.json)
RUN npm ci --no-audit --no-fund

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
