# Project
## Composer
### Installation
```sh
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

php -r "if (hash_file('sha384', 'composer-setup.php') ===
'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6')
{ echo 'Installer verified'; }
else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

php composer-setup.php

php -r "unlink('composer-setup.php');"
```
## Imageick
### Installation
```sh
# To install Imagick run bellow command:
sudo apt-get install php-imagick

# For specific PHP version:
sudo apt-get install php8.1-imagick

# Then restart apache:
sudo service apache2 restart

# To check if the extension has been installed:
php -m | grep imagick

```
### Configuration

Just before `</policymap>` in `/etc/ImageMagick-7/policy.xml` you have to add the following code : 
```html
<policy domain="coder" rights="read | write" pattern="PDF" />
```

## Qrcode-detector-decoder
### Installation
```sh
# To install the QR code decoder / reader library
composer require khanamiryan/qrcode-detector-decoder

```
## Test phpunit
### Run
```sh
# First you need to go in tests directory
cd tests

# Then run the tests
../vendor/bin/phpunit --bootstrap ../vendor/autoload.php processTest.php


