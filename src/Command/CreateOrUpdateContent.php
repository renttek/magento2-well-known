<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Command;

use Magento\Framework\App\ResourceConnection;
use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Model\Table;

class CreateOrUpdateContent
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
    ) {}

    public function execute(DTO\Content $content): void
    {
        $connection = $this->resourceConnection->getConnection('write');

        // TODO: assign stores
        // TODO: check collision with store assignment

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

        $contentId = $content->id;
        if ($contentId === null) {
            $lastInsertId = (int) $connection->fetchOne('SELECT LAST_INSERT_ID()');
            $contentId    = $lastInsertId;
        }

        $connection->delete(
            Table\ContentStore::TABLE,
            sprintf('%s = %d', Table\ContentStore::FIELD_CONTENT_ID, $contentId),
        );
        $connection->insertMultiple(
            Table\ContentStore::TABLE,
            array_map(
                static fn(int $storeId): array => [
                    Table\ContentStore::FIELD_STORE_ID   => $storeId,
                    Table\ContentStore::FIELD_CONTENT_ID => $contentId,
                ],
                $content->storeIds,
            ),
        );
    }
}
