# JBZoo / Composer-Diff


[![Build Status](https://travis-ci.org/JBZoo/Composer-Diff.svg?branch=master)](https://travis-ci.org/JBZoo/Composer-Diff)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Composer-Diff/badge.svg)](https://coveralls.io/github/JBZoo/Composer-Diff?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Diff/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Diff)    
[![Latest Stable Version](https://poser.pugx.org/JBZoo/Composer-Diff/v)](https://packagist.org/packages/JBZoo/Composer-Diff)    [![Latest Unstable Version](https://poser.pugx.org/JBZoo/Composer-Diff/v/unstable)](https://packagist.org/packages/JBZoo/Composer-Diff)    [![Dependents](https://poser.pugx.org/JBZoo/Composer-Diff/dependents)](https://packagist.org/packages/JBZoo/Composer-Diff/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/JBZoo/Composer-Diff)](https://github.com/JBZoo/Composer-Diff/issues)    [![Total Downloads](https://poser.pugx.org/JBZoo/Composer-Diff/downloads)](https://packagist.org/packages/JBZoo/Composer-Diff/stats)    [![GitHub License](https://img.shields.io/github/license/JBZoo/Composer-Diff)](https://github.com/JBZoo/Composer-Diff/blob/master/LICENSE)


## Installation

```
composer require        jbzoo/composer-diff # For specific project
composer require global jbzoo/composer-diff # As global tool
```

## Usage

```
./vendor/bin/jbzoo-composer-diff --help

Description:
  Show difference between two versions of composer.lock files

Usage:
  diff [options]

Options:
      --source=SOURCE   The file, git ref, or git ref with filename to compare FROM [default: "HEAD:composer.lock"]
      --target=TARGET   The file, git ref, or git ref with filename to compare TO [default: "./composer.lock"]
      --env=ENV         Show only selected environment. Available options: both, require, require-dev [default: "both"]
      --output=OUTPUT   Output format. Available options: console, markdown, json [default: "console"]
      --no-links        Hide all links in tables
      --strict          Return exit code if you have any difference
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```


# Output Examples

### Default view (--output=console)

Also, see colored example based on [Drupal v8.9.1 vs v9.0.1](https://travis-ci.org/github/JBZoo/Composer-Diff/jobs/705011296#L452)

```
Required by Production (require)
+-------------------+------------+-------------+-------------+------------------------------------------------------------+
| Package           | Action     | Old Version | New Version | Details                                                    |
+-------------------+------------+-------------+-------------+------------------------------------------------------------+
| vendor/downgraded | Downgraded |       2.0.0 |       1.0.0 | https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0 |
| vendor/new        | New        |           - |       1.0.0 |                                                            |
| vendor/removed    | Removed    |       1.0.0 |           - |                                                            |
| vendor/upgraded   | Upgraded   |       1.0.0 |       2.0.0 | https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0   |
+-------------------+------------+-------------+-------------+------------------------------------------------------------+
```



### Markdown Output (--output=markdown)

Source code:
```markdown
## Required by Production (require)

| Package                                                   | Action     | Old Version | New Version |                                                                           |
|-----------------------------------------------------------|------------|------------:|------------:|---------------------------------------------------------------------------|
| [vendor/downgraded](https://gitlab.com/vendor/downgraded) | Downgraded |       2.0.0 |       1.0.0 | [See details](https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0) |
| [vendor/new](https://gitlab.com/vendor/new)               | New        |           - |       1.0.0 |                                                                           |
| [vendor/removed](https://gitlab.com/vendor/removed)       | Removed    |       1.0.0 |           - |                                                                           |
| [vendor/upgraded](https://gitlab.com/vendor/upgraded)     | Upgraded   |       1.0.0 |       2.0.0 | [See details](https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0)   |
```

Rendered:

| Package                                                   | Action     | Old Version | New Version |                                                                           |
|-----------------------------------------------------------|------------|------------:|------------:|---------------------------------------------------------------------------|
| [vendor/downgraded](https://gitlab.com/vendor/downgraded) | Downgraded |       2.0.0 |       1.0.0 | [See details](https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0) |
| [vendor/new](https://gitlab.com/vendor/new)               | New        |           - |       1.0.0 |                                                                           |
| [vendor/removed](https://gitlab.com/vendor/removed)       | Removed    |       1.0.0 |           - |                                                                           |
| [vendor/upgraded](https://gitlab.com/vendor/upgraded)     | Upgraded   |       1.0.0 |       2.0.0 | [See details](https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0)   |



### JSON Output (--output=json)

```json
{
    "require": {
        "vendor\/downgraded": {
            "name": "vendor\/downgraded",
            "url": "https:\/\/gitlab.com\/vendor\/downgraded",
            "version_from": "2.0.0",
            "version_to": "1.0.0",
            "mode": "Downgraded",
            "compare": "https:\/\/gitlab.com\/vendor\/downgraded\/compare\/2.0.0...1.0.0"
        },
        "vendor\/new": {
            "name": "vendor\/new",
            "url": "https:\/\/gitlab.com\/vendor\/new",
            "version_from": null,
            "version_to": "1.0.0",
            "mode": "New",
            "compare": null
        },
        "vendor\/removed": {
            "name": "vendor\/removed",
            "url": "https:\/\/gitlab.com\/vendor\/removed",
            "version_from": "1.0.0",
            "version_to": null,
            "mode": "Removed",
            "compare": null
        },
        "vendor\/upgraded": {
            "name": "vendor\/upgraded",
            "url": "https:\/\/gitlab.com\/vendor\/upgraded",
            "version_from": "1.0.0",
            "version_to": "2.0.0",
            "mode": "Upgraded",
            "compare": "https:\/\/gitlab.com\/vendor\/upgraded\/compare\/1.0.0...2.0.0"
        }
    }
}
```

## Unit tests and check code style
```sh
make update
make test-all
```


### License

MIT
