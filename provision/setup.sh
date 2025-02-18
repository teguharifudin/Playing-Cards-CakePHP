#!/bin/bash
export DEBIAN_FRONTEND=noninteractive
apt-get update && apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip build-essential

# PHP Installation
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y php8.2 php8.2-cli php8.2-common php8.2-fpm php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-sqlite3 php8.2-intl

# Composer Installation
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Node.js Installation
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt-get install -y nodejs

# Create directories and set permissions
mkdir -p /var/www/{frontend,backend}
chown -R vagrant:vagrant /var/www
chmod -R 755 /var/www

# Create scripts directory
mkdir -p /home/vagrant/bin
chown -R vagrant:vagrant /home/vagrant/bin
chmod -R 755 /home/vagrant/bin
