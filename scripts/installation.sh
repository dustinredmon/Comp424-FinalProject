#!/bin/bash

#Instructions to use this script 
#Put folder in root
#
#chmod +x installation.sh
#sudo ./installation.sh


echo "###################################################################################"
echo "Please be Patient: Installation will start now....... It may take some time :)"
echo "###################################################################################"

#Update the repositories

sudo apt update
sudo apt upgrade -y 

echo "$(tput bold)$(tput setaf 2)Initial updates done$(tput sgr0)"
sleep 5

#Apache, Php, OpenSSH, Snort, and required packages installation

sudo apt install -y apache2 
sudo apt install -y php
sudo apt install -y php-pear php-fpm php-dev php-zip php-curl php-xmlrpc php-gd php-mysql php-mbstring php-xml libapache2-mod-php
sudo apt install -y openssh-server
sudo apt install -y gcc libpcre3-dev zlib1g-dev libluajit-5.1-dev libpcap-dev openssl libssl-dev libnghttp2-dev libdumbnet-dev bison flex libdnet

echo "$(tput bold)$(tput setaf 2)Packages installation done$(tput sgr0)"
sleep 5

#Install Composer and PHPMailer
sudo apt install -y wget composer
cd /var/www/html
composer require phpmailer/phpmailer

#MySQL secure installation.

sudo apt -y install mysql-server
mysql -u root <<EOF
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.db WHERE Db='test' OR Db='test_%';
CREATE USER IF NOT EXISTS link@localhost IDENTIFIED BY 'hero12#$';
GRANT PRIVILEGES ON *.* TO 'link'@'localhost' IDENTIFIED BY 'hero12#$';
CREATE DATABASE IF NOT EXISTS nova_prospekt;
USE nova_prospekt;
CREATE TABLE IF NOT EXISTS users (
	idUsers int(11) NOT NULL AUTO_INCREMENT, 
	uidUsers tinytext NOT NULL, 
	firstUsers tinytext NOT NULL,
	lastUsers tinytext NOT NULL, 
	emailUsers tinytext NOT NULL, 
	pwdUsers longtext NOT NULL,
	login_countUsers int(11) NOT NULL DEFAULT 0, 
	last_loginUsers TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	sq1Users enum('What was your first car?','What primary school did you attend?','In what town or city was your first full time job?'),
	sa1Users longtext NOT NULL,
	sq2Users enum('In what town or city did you meet your spouse/partner?','What is the middle name of your oldest child?','In what town or city did your mother and father meet?'),
	sa2Users longtext NOT NULL,
	bdayUsers date NOT NULL,
	statusUsers enum('Inactive', 'Active') default 'Inactive', 
	PRIMARY KEY (idUsers)
);
CREATE TABLE IF NOT EXISTS login_history (
    id INT(11) NOT NULL AUTO_INCREMENT, 
    uidUsers tinytext NOT NULL,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    userIP varbinary(16) NOT NULL,
    success ENUM('yes', 'no') NOT NULL DEFAULT 'no',
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS pwdReset (
    pwdResetId INT(11) NOT NULL AUTO_INCREMENT, 
    pwdResetEmail text NOT NULL,
    pwdResetSelector text NOT NULL,
    pwdResetToken longtext NOT NULL,
    pwdResetExpires text NOT NULL,
    PRIMARY KEY (pwdResetId)
);
FLUSH PRIVILEGES;
exit
EOF

echo "$(tput bold)$(tput setaf 2)Database setup$(tput sgr0)"
sleep 5

#Restart all the installed services to verify that everything is installed properly

echo -e "\n"

service apache2 restart && service mysql restart > /dev/null

echo -e "\n"

php -v
apache2 -v
mysqld --version

echo "$(tput bold)$(tput setaf 2)Installed Successfully$(tput sgr0)"
echo -e "\n"

exit 0
