#!/bin/bash

cd ../
echo "Download last composer.phar"
php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
php composer-setup.php
./composer.phar clear-cache
./composer.phar install --dev
cd -

../vendor/bin/phpunit --coverage-clover build/logs/clover.xml

CODECLIMATE_REPO_TOKEN=77b1ac4ca1eb8136388443cb29a996c73e31e1bc8f8328c98d30809a6f62e198  ../vendor/bin/test-reporter