<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Magento\Framework\App\ResourceConnection;
use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Model\Table;

use function sprintf;

class GetContentByIdentifier
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Modifier\AddStoreFilter $addStoreFilter,
    ) {}

    public function execute(string $identifier, ?int $storeId = null): ?DTO\Content
    {
        $connection = $this->resourceConnection->getConnection('read');
        $table      = $this->resourceConnection->getTableName(Table\Content::TABLE);

        $query = $connection->select()
            ->from(['c' => $table])
            ->joinLeft(
                ['cs' => Table\ContentStore::TABLE],
                sprintf('c.%s = cs.%s', Table\Content::FIELD_ID, Table\ContentStore::FIELD_CONTENT_ID),
                [sprintf('GROUP_CONCAT(cs.%s) as %s', Table\ContentStore::FIELD_STORE_ID, Table\Content::JOIN_STORE_IDS)],
            )
            ->where(sprintf('c.%s = ?', Table\Content::FIELD_IDENTIFIER), $identifier)
            ->limit(1);

        if ($storeId !== null) {
            $this->addStoreFilter->execute($query, $storeId);
        }

        $content = $connection->fetchRow($query);
        if (!is_array($content)) {
            return null;
        }

        /** @var string $storeIds */
        $storeIds = $content[Table\Content::JOIN_STORE_IDS] ?? '';
        $storeIds = explode(',', $storeIds);
        $storeIds = array_filter($storeIds, static fn(string $s): bool => $s !== '');
        $storeIds = array_map(intval(...), $storeIds);
        $content[Table\Content::JOIN_STORE_IDS] = $storeIds;

        return DTO\Content::fromArrayOrNull($content);
    }
}
