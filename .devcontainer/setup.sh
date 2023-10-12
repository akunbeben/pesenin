#!/bin/bash

# Generate random credentials
DB_DATABASE="laravel_db_$(date +%s)"
DB_USERNAME="laravel_user_$(date +%s)"
DB_PASSWORD=$(openssl rand -base64 12)

# Replace the placeholders in the Laravel .env file with the generated credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

# Install Laravel Vite and run the build
composer install
npm install
npm run dev

# Run Laravel migrations
php artisan migrate

# Start the application
php artisan serve --host=0.0.0.0 --port=8000
