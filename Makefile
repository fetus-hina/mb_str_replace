all: init

init: install-composer depends-install

install-composer: composer.phar

depends-install: install-composer
	php composer.phar install

depends-update: install-composer
	php composer.phar self-update
	php composer.phar update

test:
	vendor/bin/phpunit

check-style:
	vendor/bin/phpmd src text codesize,controversial,design,naming,unusedcode
	vendor/bin/phpcs --standard=PSR2 src test

fix-style:
	vendor/bin/phpcbf --standard=PSR2 src test

clean:
	rm -rf vendor composer.phar clover.xml

composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: all init install-composer depends-install depends-update test clean check-style fix-style
