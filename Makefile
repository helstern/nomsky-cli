SHELL=/bin/bash
DIR := $(shell dirname $(abspath $(lastword $(MAKEFILE_LIST))))
PHPFLAGS=
PHPENV=


ifdef XDEBUG_CONFIG
	PHPFLAGS += -d xdebug.remote_enable=1
endif

ifdef PHP_IDE_CONFIG
	PHPENV += PHP_IDE_CONFIG=$(PHP_IDE_CONFIG)
endif

initialize:
	curl -LSs https://box-project.github.io/box2/installer.php | php ; mv box.phar $(DIR)/bin/box.phar

validate:
	php -v

compile: validate
	php ./bin/composer.phar install

test: compile
	bash bin/phpunit.sh --configuration src/test/config/phpunit.xml.dist

package:
	ln -s src/package/box.json box.json
	php --define phar.readonly=0 bin/box.phar build
	rm box.json

.PHONY: initialize vagrant





