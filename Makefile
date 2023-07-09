#
# JBZoo Toolbox - Composer-Diff.
#
# This file is part of the JBZoo Toolbox project.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @license    MIT
# @copyright  Copyright (C) JBZoo.com, All rights reserved.
# @see        https://github.com/JBZoo/Composer-Diff
#

.PHONY: build

ifneq (, $(wildcard ./vendor/jbzoo/codestyle/src/init.Makefile))
    include ./vendor/jbzoo/codestyle/src/init.Makefile
endif


build: ##@Project Install all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@composer install --optimize-autoloader --no-progress
	@make build-phar
	@make create-symlink


build-phar-issue:
	$(call title,"Building PHAR")
	$(call download_phar,$(BOX_PHAR),"box")
	rm -f ./build/composer-diff.phar || true
	$(PHP_BIN) ./vendor/bin/box.phar compile -v
	$(PHP_BIN) ./build/composer-diff.phar diff --help


update: ##@Project Install/Update all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@composer update --optimize-autoloader --no-progress
	@make build-phar
	@make create-symlink
	$(call title,"Show difference in composer.lock")
	@$(PHP_BIN) `pwd`/vendor/bin/composer-diff --output=markdown


create-symlink: ##@Project Create Symlink (alias for testing)
	@ln -sfv `pwd`/build/composer-diff.phar `pwd`/vendor/bin/composer-diff


build-docker: ##@Project Building Docker Image
	$(call title,"Building Docker Image")
	@docker build -t jbzoo-composer-diff .


test-all: ##@Project Run all project tests at once
	@make test
	@make test-drupal
	@make test-manual
	@make codestyle
	@-make report-merge-coverage


test-drupal:
	$(call title,"Testing real project - Drupal")
	@echo ""
	@echo "  Example. Comparing Drupal v8.9.1 vs v9.0.1 (required only)"
	@echo ""
	@php ./composer-diff                                                  \
        --source="`pwd`/tests/fixtures/testDrupal/composer-8.9.1.lock"    \
        --target="`pwd`/tests/fixtures/testDrupal/composer-9.0.1.lock"    \
        --env=require                                                     \
        -vvv


test-manual:
	$(call title,"Testing output")
	@echo ""
	@echo "  Example. Just dummy example"
	@echo ""
	@php ./composer-diff                                                                      \
        --source="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-from.json"    \
        --target="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-to.json"      \
        --env=require                                                                         \
        -vvv
	@php ./composer-diff                                                                      \
        --source="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-from.json"    \
        --target="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-to.json"      \
        --env=require                                                                         \
        --output=markdown                                                                     \
        -vvv
	@php ./composer-diff                                                                      \
        --source="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-from.json"    \
        --target="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-to.json"      \
        --env=require                                                                         \
        --output=json                                                                         \
        -vvv
