#!/bin/bash

if [ "$1" = 'install' ]
	then
		cd /project
		php -r "readfile('https://getcomposer.org/installer');" | php
		/project/composer.phar install
	elif [ "$1" = 'update' ]
		then
			/project/composer.phar update
	else
		/project/vendor/bin/phpunit --bootstrap /project/vendor/autoload.php /project/tests/
fi
