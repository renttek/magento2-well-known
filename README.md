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

Content can be managed int the backend / admin area, under `Stores > /.well-known/ > Manage Content`.
The `Identifier` of each entry is the path matched against the url: `https://example.com/.well-known/<identifier>`.
Each entry can also be set to a list of specific stores or global, if no store is set. (with automatic fallback to the global when loading the content)

## Extending / Custom provider for /.well-known/ content

If more control is needed, the extension also provides the `Renttek\WellKnown\Model\WellKnownProviderInterface` to implement custom providers for /.well-known/ content.
To do that, implement the `WellKnownProviderInterface` in your own code and register the provider with the `Renttek\WellKnown\Model\WellKnownProviderPool`.

`frontend/di.xml`:
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">rguments>
    </type>
    <type name="Renttek\WellKnown\Model\WellKnownProviderPool">
        <arguments>
            <argument name="providers" xsi:type="array">
                <item name="my_custom_provider" xsi:type="object" sortOrder="10">Vendor\Module\Model\CustomProvider</item>
            </argument>
        </arguments>
    </type>
</config>
```
