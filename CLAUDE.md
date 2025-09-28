# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

JBZoo Composer-Diff is a PHP CLI tool that compares two versions of composer.lock files and shows what packages have changed. It's particularly useful for visualizing dependency changes after running `composer update`, providing detailed output in console, markdown, or JSON formats with links to compare views on package repositories.

## Core Architecture

### Command-Line Interface
- Built on **Symfony Console** and **JBZoo CLI** framework
- Single command application with `DiffAction` as the main command (`src/Commands/DiffAction.php`)
- Executable binary: `composer-diff` (defined in composer.json bin section)
- Entry point through the binary file `/composer-diff`

### Core Classes
- **`Diff`** (`src/Diff.php`) - Main comparison logic with version change detection (New, Removed, Changed, Upgraded, Downgraded)
- **`Comparator`** (`src/Comparator.php`) - Orchestrates the comparison process between composer.lock files
- **`ComposerLock`** (`src/ComposerLock.php`) - Parses and extracts package data from composer.lock files
- **`Package`** (`src/Package.php`) - Represents individual package with version and URL information
- **`Url`** (`src/Url.php`) - Generates comparison URLs for different Git hosting platforms

### Output Renderers (`src/Renders/`)
- **`Console`** - Default colored table output for terminal
- **`Markdown`** - Generates markdown tables for PRs/documentation
- **`JsonOutput`** - Machine-readable JSON format
- **`AbstractRender`** - Base class for all output formats

## Common Commands

### Development Setup
```bash
make build           # Install dependencies and build phar
make update          # Install/update dependencies and build phar
```

### Testing
```bash
make test            # Run PHPUnit tests
make test-all        # Run all tests and code quality checks
make test-drupal     # Test with real Drupal upgrade scenario
make test-manual     # Run manual test examples with sample data
```

### Code Quality (via JBZoo Toolbox)
```bash
make codestyle       # Run all linters and code style checks
```

### Build Process
```bash
make build-phar      # Build composer-diff.phar executable
make create-symlink  # Create symlink vendor/bin/composer-diff → build/composer-diff.phar
```

### Usage Examples
```bash
# Basic usage (compares HEAD:composer.lock with ./composer.lock)
php ./vendor/bin/composer-diff

# Compare specific files
php ./composer-diff --source="old/composer.lock" --target="new/composer.lock"

# Different output formats
php ./composer-diff --output=markdown
php ./composer-diff --output=json

# Filter environments
php ./composer-diff --env=require      # Production dependencies only
php ./composer-diff --env=require-dev  # Development dependencies only
```

## Package Detection Logic

The tool identifies changes by:
1. **Version Comparison**: Uses Composer's semantic versioning via `composer/semver`
2. **Hash Detection**: Recognizes dev packages with commit hashes (40-character strings without dots)
3. **URL Generation**: Creates compare links for GitHub, GitLab, and Bitbucket repositories
4. **Change Classification**: Categorizes as New, Removed, Upgraded, Downgraded, Changed, or Same

## Testing Strategy

### Test Structure
- **Unit Tests**: `tests/` directory with comprehensive coverage of all classes
- **Fixture Data**: `tests/fixtures/` contains realistic composer.lock samples for various scenarios
- **Integration Tests**: Real-world examples like Drupal version comparisons
- **Manual Testing**: Built-in examples in Makefile for output verification

### Test Scenarios Covered
- New package installation
- Package removal
- Version upgrades/downgrades
- Dev branch changes (hash-based versions)
- Complex dependency matrices
- Different environment filtering (require vs require-dev)

## Build System

### JBZoo Toolbox Integration
Uses JBZoo's standardized development toolchain:
- Inherits comprehensive Makefile targets from `vendor/jbzoo/codestyle/src/init.Makefile`
- Automated code style, static analysis, and testing via `jbzoo/toolbox-dev`
- Consistent development experience across JBZoo ecosystem

### PHAR Distribution
- Builds standalone `build/composer-diff.phar` executable
- Supports global installation via `composer global require jbzoo/composer-diff`
- GitHub releases provide downloadable phar files

## Dependencies

### Production
- `php: ^8.2` with `ext-json` and `ext-filter`
- `jbzoo/cli: ^7.2.4` - Enhanced Symfony Console wrapper
- `jbzoo/data: ^7.2` - Data manipulation utilities
- `jbzoo/markdown: ^7.0.2` - Markdown generation
- `symfony/console: >=6.4`, `symfony/process: >=6.4`
- `composer/semver: >=1.0` - Version comparison logic

### Development
- `jbzoo/toolbox-dev: ^7.3` - Complete development toolchain
- `composer/composer: >=2.0` - For testing with real composer.lock files

## File Structure Patterns

```
src/
├── Commands/DiffAction.php    # Main CLI command
├── Renders/                   # Output format implementations
├── Diff.php                   # Core comparison logic
├── Comparator.php            # Comparison orchestration
├── ComposerLock.php          # Lock file parsing
├── Package.php               # Package representation
├── Url.php                   # URL generation
└── Exception.php             # Custom exceptions

tests/
├── fixtures/                 # Test composer.lock files
│   ├── testComparingComplex/ # Complex change scenarios
│   ├── testDrupal/           # Real Drupal upgrade data
│   └── [scenario-name]/      # Various test cases
└── *Test.php                 # Unit tests for each class
```