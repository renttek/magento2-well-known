<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Command;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Model\Table;
use Renttek\WellKnown\Query;

class CreateOrUpdateContent
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
    ) {}

    public function execute(DTO\Content $content): void
    {
        $connection = $this->resourceConnection->getConnection('write');
        $storeIds   = $this->getStoreIds($content);

        try {
            $connection->beginTransaction();

            $contentId = $this->createOrUpdateContent($connection, $content);
            $this->updateStoreAssignments($connection, $contentId, $storeIds);

            $connection->commit();
        } catch (Exception) {
            $connection->rollBack();
        }
    }

    private function createOrUpdateContent(AdapterInterface $connection, DTO\Content $content): int
    {
        $connection->insertOnDuplicate(
            table : Table\Content::TABLE,
            data  : [
                Table\Content::FIELD_ID         => $content->id,
                Table\Content::FIELD_IDENTIFIER => $content->identifier,
                Table\Content::FIELD_TYPE       => $content->type->value,
                Table\Content::FIELD_CONTENT    => $content->content,
            ],
            fields: [
                Table\Content::FIELD_IDENTIFIER,
                Table\Content::FIELD_TYPE,
                Table\Content::FIELD_CONTENT,
            ],
        );

        return $content->id ?? (int) $connection->fetchOne('SELECT LAST_INSERT_ID()');
    }

    /**
     * @param list<int> $storeIds
     */
    private function updateStoreAssignments(
        AdapterInterface $connection,
        int              $contentId,
        array            $storeIds,
    ): void {
        $connection->delete(
            Table\ContentStore::TABLE,
            sprintf('%s = %d', Table\ContentStore::FIELD_CONTENT_ID, $contentId),
        );

        if ($storeIds === []) {
            return;
        }

        $connection->insertMultiple(
            Table\ContentStore::TABLE,
            array_map(
                static fn(int $storeId): array => [
                    Table\ContentStore::FIELD_STORE_ID   => $storeId,
                    Table\ContentStore::FIELD_CONTENT_ID => $contentId,
                ],
                $storeIds,
            ),
        );
    }

    /**
     * @return list<int>
     */
    private function getStoreIds(DTO\Content $content): array
    {
        return array_values(
            array_filter(
                $content->storeIds,
                static fn(int $storeId): bool => $storeId !== 0,
            ),
        );
    }
}
