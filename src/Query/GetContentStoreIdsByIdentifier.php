<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Store\Model\Store;
use Renttek\WellKnown\Model\Table;

class GetContentStoreIdsByIdentifier
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
    ) {}

    /**
     * @return non-empty-list<int>
     */
    public function execute(string $identifier): array
    {
        $connection        = $this->resourceConnection->getConnection('read');
        $contentTable      = $this->resourceConnection->getTableName(Table\Content::TABLE);
        $contentStoreTable = $this->resourceConnection->getTableName(Table\ContentStore::TABLE);

        $query = $connection->select()
            ->from(['cs' => $contentStoreTable], [Table\ContentStore::FIELD_STORE_ID])
            ->joinInner(
                ['c' => $contentTable],
                $this->getJoinCondition($connection, $identifier),
            );

        /** @var array<array-key, string> $storeIds */
        $storeIds = $connection->fetchCol($query);
        $storeIds = array_filter($storeIds, static fn(string $line): bool => $line !== '');
        $storeIds = array_map(intval(...), $storeIds);
        $storeIds = array_unique($storeIds);

        /** @var list<int> $storeIds */
        return $storeIds !== []
            ? $storeIds
            : [Store::DEFAULT_STORE_ID];

    }

    private function getJoinCondition(AdapterInterface $connection, string $identifier): string
    {
        $joinCondition = \sprintf(
            'cs.%s = c.%s AND c.%s = ?',
            Table\ContentStore::FIELD_CONTENT_ID,
            Table\Content::FIELD_ID,
            Table\Content::FIELD_IDENTIFIER,
        );

        return $connection->quoteInto($joinCondition, $identifier);
    }
}
