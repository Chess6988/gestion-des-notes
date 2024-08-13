# Utiliser une image PHP officielle comme base
FROM php:8.0-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install mysqli

# Copier les fichiers de votre projet dans le conteneur
COPY . /var/www/html/

# Exposer le port sur lequel l'application s'exécute
EXPOSE 80