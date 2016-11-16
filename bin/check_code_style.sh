#!/usr/bin/env bash
cd "$(dirname "$0")/../"
php vendor/bin/php-cs-fixer fix --dry-run --diff