<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const string ENABLED              = 'renttek_wellknown/general/enabled';
    private const string EXCLUDED_IDENTIFIERS = 'renttek_wellknown/general/excluded_identifiers';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
    ) {}

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return list<string>
     */
    public function getExcludedPaths(?int $storeId = null): array
    {
        $excludedIdentifiers = $this->scopeConfig->getValue(self::EXCLUDED_IDENTIFIERS, ScopeInterface::SCOPE_STORE, $storeId);
        if (!is_string($excludedIdentifiers)) {
            return [];
        }

        $excludedIdentifiers = str_replace("\r\n", "\n", $excludedIdentifiers);
        $excludedIdentifiers = explode("\n", $excludedIdentifiers);
        $excludedIdentifiers = array_filter($excludedIdentifiers, static fn(string $line): bool => $line !== '');

        return array_values($excludedIdentifiers);
    }
}
