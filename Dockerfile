# Utiliser une image PHP officielle comme base
FROM php:8.0-apache

# Copier les fichiers de votre projet dans le conteneur
COPY . /var/www/html/

# Exposer le port sur lequel l'application s'exécute
EXPOSE 80
