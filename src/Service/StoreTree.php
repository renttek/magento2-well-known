<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Service;

use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @phpstan-type StoreData array{id: int, name: string}
 * @phpstan-type GroupData array{name: string, stores: list<StoreData>}
 * @phpstan-type WebsiteData array{name: string, groups: list<GroupData>}
 */
class StoreTree
{
    /**
     * @var list<StoreInterface>
     */
    private array $stores;

    /**
     * @var list<GroupInterface>
     */
    private array $groups;

    /**
     * @var list<WebsiteInterface>
     */
    private array $websites;

    /**
     * @phpstan-var list<WebsiteData>
     */
    private array $fullTree;

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
    ) {}

    /**
     * @phpstan-return list<WebsiteData>
     */
    public function getFullTree(): array
    {
        return $this->fullTree ??= array_map(
            $this->getWebsiteData(...),
            $this->getAllWebsites(),
        );
    }

    /**
     * @param list<int> $storeIds
     *
     * @phpstan-return list<WebsiteData>
     */
    public function getFilteredTree(array $storeIds): array
    {
        $tree = $this->getFullTree();

        // Filter by store ids
        $tree = array_map(
            static function ($website) use ($storeIds) {
                $website['groups'] = array_map(
                    static function ($group) use ($storeIds) {
                        $group['stores'] = array_filter(
                            $group['stores'],
                            static fn(array $s): bool => in_array($s['id'], $storeIds, true),
                        );
                        return $group;
                    },
                    $website['groups'],
                );
                return $website;
            },
            $tree,
        );

        // prune groups tree
        $tree = array_map(
            static function ($website) {
                $website['groups'] = array_filter(
                    $website['groups'],
                    static fn(array $g): bool => $g['stores'] !== [],
                );
                return $website;
            },
            $tree,
        );

        // prune websites
        $tree = array_filter($tree, static fn(array $w): bool => $w['groups'] !== []);

        return array_values($tree);
    }

    /**
     * @phpstan-return WebsiteData
     */
    private function getWebsiteData(WebsiteInterface $website): array
    {
        return [
            'name'   => $website->getName(),
            'groups' => array_map(
                $this->getGroupData(...),
                $this->getGroupsByWebsiteId((int) $website->getId()),
            ),
        ];
    }

    /**
     * @phpstan-return GroupData
     */
    private function getGroupData(GroupInterface $group): array
    {
        return [
            'name'   => $group->getName(),
            'stores' => array_map(
                $this->getStoreData(...),
                $this->getStoresByGroupId((int) $group->getId()),
            ),
        ];
    }

    /**
     * @phpstan-return StoreData
     */
    private function getStoreData(StoreInterface $store): array
    {
        /** @noinspection PhpCastIsUnnecessaryInspection */
        return [
            'id'   => (int) $store->getId(),
            'name' => $store->getName(),
        ];
    }

    /**
     * @return list<StoreInterface>
     */
    private function getAllStores(): array
    {
        return $this->stores ??= array_values($this->storeManager->getStores());
    }

    /**
     * @return list<StoreInterface>
     */
    private function getStoresByGroupId(int $groupId): array
    {
        return array_values(
            array_filter(
                $this->getAllStores(),
                static fn(StoreInterface $s): bool => $groupId === (int) $s->getStoreGroupId(),
            ),
        );
    }

    /**
     * @return list<GroupInterface>
     */
    private function getAllGroups(): array
    {
        return $this->groups ??= array_values($this->storeManager->getGroups());
    }

    /**
     * @return list<GroupInterface>
     */
    private function getGroupsByWebsiteId(int $websiteId): array
    {
        return array_values(
            array_filter(
                $this->getAllGroups(),
                static fn(GroupInterface $g): bool => $websiteId === (int) $g->getWebsiteId(),
            ),
        );
    }

    /**
     * @return list<WebsiteInterface>
     */
    private function getAllWebsites(): array
    {
        return $this->websites ??= array_values($this->storeManager->getWebsites());
    }
}
