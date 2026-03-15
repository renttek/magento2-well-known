<?php

declare(strict_types=1);

namespace Renttek\WellKnown\ViewModel\Adminhtml;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Renttek\WellKnown\Service\StoreTree;

use function array_any;

/**
 * @phpstan-import-type StoreData from StoreTree
 * @phpstan-import-type GroupData from StoreTree
 * @phpstan-import-type WebsiteData from StoreTree
 */
class StoreList implements ArgumentInterface
{
    public function __construct(
        private readonly StoreTree $storeTree,
    ) {}

    /**
     * @param list<int> $storeIds
     */
    public function isAllStores(array $storeIds): bool
    {
        return $storeIds === []
            || array_any($storeIds, fn(int $s): bool => $s === 0);
    }

    /**
     * @param list<int> $storeIds
     *
     * @return list<WebsiteData>
     */
    public function getStoreTree(array $storeIds): array
    {
        return $this->storeTree->getFilteredTree($storeIds);
    }
}
