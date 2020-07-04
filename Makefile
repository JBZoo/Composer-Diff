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
	@make codestyle
	@-make report-merge-coverage


test-drupal:
	@php ./jbzoo-composer-diff                                            \
        --source="`pwd`/tests/fixtures/testDrupal/composer-8.9.1.lock"    \
        --target="`pwd`/tests/fixtures/testDrupal/composer-9.0.1.lock"    \
        --env=require                                                     \
        -vvv


test-internal:
	@php ./jbzoo-composer-diff                                                                \
        --source="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-from.json"    \
        --target="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-to.json"      \
        --env=require                                                                         \
        -vvv
	@php ./jbzoo-composer-diff                                                                \
        --source="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-from.json"    \
        --target="`pwd`/tests/fixtures/testComparingComplexSimple/composer-lock-to.json"      \
        --output=markdown                                                                     \
        -vvv
