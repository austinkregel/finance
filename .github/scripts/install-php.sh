#!/usr/bin/env bash

set -e

sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt install -y php8.0-{common,cli,gd,curl,mysql,mbstring,dom,xml,simplexml}
curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
