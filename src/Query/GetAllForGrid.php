<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Magento\Framework\App\ResourceConnection;
use Renttek\WellKnown\Model\Table;
use function sprintf;

class GetAllForGrid
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
    ) {}

    /**
     * @return list<array{content_id: int, identifier: string, store_ids: list<int>}>
     */
    public function execute(): array
    {
        $connection        = $this->resourceConnection->getConnection('read');
        $contentTable      = $this->resourceConnection->getTableName(Table\Content::TABLE);
        $contentStoreTable = $this->resourceConnection->getTableName(Table\ContentStore::TABLE);

        $query = $connection->select()
            ->from(
                ['c' => $contentTable],
                [Table\Content::FIELD_ID, Table\Content::FIELD_IDENTIFIER]
            )
            ->joinLeft(
                ['cs' => $contentStoreTable],
                sprintf(
                    'cs.%s = c.%s',
                    Table\ContentStore::FIELD_CONTENT_ID,
                    Table\Content::FIELD_ID,
                ),
                [
                    sprintf(
                        'GROUP_CONCAT(cs.%s) as store_ids',
                        Table\ContentStore::FIELD_STORE_ID
                    )
                ]
            )
            ->group('c.' . Table\ContentStore::FIELD_CONTENT_ID);

        $rows = $connection->fetchAssoc($query);
        $rows = array_map($this->convertContentId(...), $rows);
        $rows = array_map($this->convertStoreIds(...), $rows);

        return array_values($rows);
    }

    /**
     * @param array{content_id: string, ...} $row
     *
     * @return array{content_id: int, ...}
     */
    private function convertContentId(array $row): array
    {
        $row['content_id'] = (int)$row['content_id'];

        return $row;
    }

    /**
     * @param array{store_ids: string|null, ...} $row
     *
     * @return array{store_ids: list<int>, ...}
     */
    private function convertStoreIds(array $row): array
    {
        $row['store_ids'] ??= [];

        if (is_string($row['store_ids']) && $row['store_ids'] !== '') {
            $row['store_ids'] = array_map(
                intval(...),
                explode(',', $row['store_ids'])
            );
        }

        return $row;
    }
}
