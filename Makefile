all: composer.phar vendor test composer.lock.travis

test: vendor
	vendor/bin/phpunit

check-style: vendor
	vendor/bin/phpcs --standard=PSR2 src

fix-style: vendor
	vendor/bin/phpcbf --standard=PSR2 src

clean:
	rm -rf vendor composer.phar composer.lock clover.xml

composer.phar:
	curl -sS https://getcomposer.org/installer | php

composer.lock: composer.phar composer.json
	php composer.phar update

composer.lock.travis: composer.lock
	cp composer.lock composer.lock.travis

vendor: composer.phar composer.lock
	php composer.phar install
	touch vendor

.PHONY: all test clean check-style fix-style
