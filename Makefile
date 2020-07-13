#
# JBZoo Toolbox - Composer-Diff
#
# This file is part of the JBZoo Toolbox project.
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# @package    Composer-Diff
# @license    MIT
# @copyright  Copyright (C) JBZoo.com, All rights reserved.
# @link       https://github.com/JBZoo/Composer-Diff
#


ifneq (, $(wildcard ./vendor/jbzoo/codestyle/src/init.Makefile))
    include ./vendor/jbzoo/codestyle/src/init.Makefile
endif


update: ##@Project Install/Update all 3rd party dependencies
	$(call title,"Install/Update all 3rd party dependencies")
	@echo "Composer flags: $(JBZOO_COMPOSER_UPDATE_FLAGS)"
	@composer update $(JBZOO_COMPOSER_UPDATE_FLAGS)


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
