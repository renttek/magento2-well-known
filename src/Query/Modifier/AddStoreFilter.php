<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query\Modifier;

use Magento\Framework\DB\Select;
use Renttek\WellKnown\Model\Table;

class AddStoreFilter
{
    public function execute(Select $query, int $storeId, string $contentTableAlias = 'c'): void
    {
        $query->joinLeft(
            ['cs' => Table\ContentStore::TABLE],
            sprintf(
                'cs.%s = %s.%s',
                Table\ContentStore::FIELD_CONTENT_ID,
                $contentTableAlias,
                Table\Content::FIELD_ID,
            ),
            [],
        );
        $query->where(sprintf(
            '(cs.%1$s IN (0, %2$d) OR cs.%1$s IS NULL)',
            Table\ContentStore::FIELD_STORE_ID,
            $storeId,
        ));
        $query->order(sprintf('cs.%s DESC', Table\ContentStore::FIELD_STORE_ID));
    }
}
