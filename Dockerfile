FROM php:8.2-apache

# Ma'lumotlar bazasi (SQL) bilan ishlash uchun PDO kengaytmalarini o'rnatamiz
RUN docker-php-ext-install pdo pdo_mysql

# Loyihadagi barcha fayllarni serverga nusxalaymiz
COPY . /var/www/html/

# Portni ochamiz (Render ushbu port orqali botni tarmoqqa bog'laydi)
EXPOSE 80
