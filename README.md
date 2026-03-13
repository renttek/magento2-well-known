# Renttek_WellKnown

This extension adds a dedicated section to the Admin Panel for managing "files" within the /.well-known/ directory.
It allows you to quickly create and serve plain-text or JSON content for specific paths (such as apple-developer-merchantid-domain-association or security.txt) without needing to touch the server's filesystem.

## Installation

```shell
composer require renttek/magento2-well-known
bin/magento module:enable Renttek_WellKnown
bin/magento setup:upgrade
```

## Usage

TBD (describe content management in the admin ui)

