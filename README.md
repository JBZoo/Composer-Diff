# JBZoo / Composer-Diff

See what packages have changed after you run `composer update` by comparing `composer.lock` to the  `git show HEAD:composer.lock`.

[![Build Status](https://travis-ci.org/JBZoo/Composer-Diff.svg?branch=master)](https://travis-ci.org/JBZoo/Composer-Diff)    [![Coverage Status](https://coveralls.io/repos/JBZoo/Composer-Diff/badge.svg)](https://coveralls.io/github/JBZoo/Composer-Diff?branch=master)    [![Psalm Coverage](https://shepherd.dev/github/JBZoo/Composer-Diff/coverage.svg)](https://shepherd.dev/github/JBZoo/Composer-Diff)    
[![Latest Stable Version](https://poser.pugx.org/JBZoo/Composer-Diff/v)](https://packagist.org/packages/JBZoo/Composer-Diff)    [![Latest Unstable Version](https://poser.pugx.org/JBZoo/Composer-Diff/v/unstable)](https://packagist.org/packages/JBZoo/Composer-Diff)    [![Dependents](https://poser.pugx.org/JBZoo/Composer-Diff/dependents)](https://packagist.org/packages/JBZoo/Composer-Diff/dependents?order_by=downloads)    [![GitHub Issues](https://img.shields.io/github/issues/JBZoo/Composer-Diff)](https://github.com/JBZoo/Composer-Diff/issues)    [![Total Downloads](https://poser.pugx.org/JBZoo/Composer-Diff/downloads)](https://packagist.org/packages/JBZoo/Composer-Diff/stats)    [![GitHub License](https://img.shields.io/github/license/JBZoo/Composer-Diff)](https://github.com/JBZoo/Composer-Diff/blob/master/LICENSE)


## Installation

```
composer require        jbzoo/composer-diff # For specific project
composer require global jbzoo/composer-diff # As global tool
```

## Usage

```bash
composer update
# don't commit yet!
jbzoo-composer-diff
```

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



```
PHP Production Dependencies (require)
+-------------------+------------+--------------------+---------------------+---------------------------------------------------------------+
| Package           | Action     |        Old Version |         New Version | Details                                                       |
+-------------------+------------+--------------------+---------------------+---------------------------------------------------------------+
| vendor/downgraded | Downgraded |              2.0.0 |               1.0.0 | https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0    |
| vendor/new        | New        |                  - |               1.0.0 |                                                               |
| vendor/no-tag     | Changed    | dev-master@bbc0fba |  dev-master@f2f9280 | https://gitlab.com/vendor/package-1/compare/bbc0fba...f2f9280 |
| vendor/no-tag-new | New        |                  - | dev-develop@a999014 |                                                               |
| vendor/removed    | Removed    |              1.0.0 |                   - |                                                               |
| vendor/upgraded   | Upgraded   |              1.0.0 |               2.0.0 | https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0      |
+-------------------+------------+--------------------+---------------------+---------------------------------------------------------------+
```

Rendered in your terminal:
![Dummy example](https://raw.githubusercontent.com/JBZoo/Composer-Diff/master/resources/dummy.png)

Also, see [colored example in travis-ci](https://travis-ci.org/github/JBZoo/Composer-Diff/jobs/705031632#L414)
![Real project](https://raw.githubusercontent.com/JBZoo/Composer-Diff/master/resources/drupal.png)



### Markdown Output (--output=markdown)

Source code:
```markdown
## PHP Production Dependencies (require)

| Package                                                    | Action     |        Old Version |         New Version |                                                                              |
|:-----------------------------------------------------------|:-----------|-------------------:|--------------------:|:-----------------------------------------------------------------------------|
| [vendor/downgraded](https://gitlab.com/vendor/downgraded)  | Downgraded |              2.0.0 |               1.0.0 | [See details](https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0)    |
| [vendor/new](https://gitlab.com/vendor/new)                | New        |                  - |               1.0.0 |                                                                              |
| [vendor/no-tag](https://gitlab.com/vendor/package-1)       | Changed    | dev-master@bbc0fba |  dev-master@f2f9280 | [See details](https://gitlab.com/vendor/package-1/compare/bbc0fba...f2f9280) |
| [vendor/no-tag-new](https://gitlab.com/vendor-1/package-1) | New        |                  - | dev-develop@a999014 |                                                                              |
| [vendor/removed](https://gitlab.com/vendor/removed)        | Removed    |              1.0.0 |                   - |                                                                              |
| [vendor/upgraded](https://gitlab.com/vendor/upgraded)      | Upgraded   |              1.0.0 |               2.0.0 | [See details](https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0)      |
```

Rendered in your readme or PR/MR description:

| Package                                                    | Action     |        Old Version |         New Version |                                                                              |
|:-----------------------------------------------------------|:-----------|-------------------:|--------------------:|:-----------------------------------------------------------------------------|
| [vendor/downgraded](https://gitlab.com/vendor/downgraded)  | Downgraded |              2.0.0 |               1.0.0 | [See details](https://gitlab.com/vendor/downgraded/compare/2.0.0...1.0.0)    |
| [vendor/new](https://gitlab.com/vendor/new)                | New        |                  - |               1.0.0 |                                                                              |
| [vendor/no-tag](https://gitlab.com/vendor/package-1)       | Changed    | dev-master@bbc0fba |  dev-master@f2f9280 | [See details](https://gitlab.com/vendor/package-1/compare/bbc0fba...f2f9280) |
| [vendor/no-tag-new](https://gitlab.com/vendor-1/package-1) | New        |                  - | dev-develop@a999014 |                                                                              |
| [vendor/removed](https://gitlab.com/vendor/removed)        | Removed    |              1.0.0 |                   - |                                                                              |
| [vendor/upgraded](https://gitlab.com/vendor/upgraded)      | Upgraded   |              1.0.0 |               2.0.0 | [See details](https://gitlab.com/vendor/upgraded/compare/1.0.0...2.0.0)      |



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
        "vendor\/no-tag": {
            "name": "vendor\/no-tag",
            "url": "https:\/\/gitlab.com\/vendor\/package-1",
            "version_from": "dev-master@bbc0fba",
            "version_to": "dev-master@f2f9280",
            "mode": "Changed",
            "compare": "https:\/\/gitlab.com\/vendor\/package-1\/compare\/bbc0fba...f2f9280"
        },
        "vendor\/no-tag-new": {
            "name": "vendor\/no-tag-new",
            "url": "https:\/\/gitlab.com\/vendor-1\/package-1",
            "version_from": null,
            "version_to": "dev-develop@a999014",
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


## Roadmap

 * [ ] Supporting Drupal repos. [For example](https://git.drupalcode.org/project/fast_404).
 * [ ] Add action in the composer via API like `composer lock-diff`.
 * [ ] Fixes [the same issue](https://github.com/davidrjonas/composer-lock-diff/issues/26) with complex/custom name of tag.
 * [ ] No warp links for markdown format.
 * [ ] (?) Support MS Windows... 


## Unit tests and check code style
```sh
make update
make test-all
```

### PS 
Special thanks to the project [davidrjonas/composer-lock-diff](https://github.com/davidrjonas/composer-lock-diff) which inspired me to make a great utility :)

### License

MIT
