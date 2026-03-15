<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Assert\Assertion;
use Magento\Framework\App\ResourceConnection;
use Renttek\WellKnown\Model\Table;

class GetAllIdentifiers
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Modifier\AddStoreFilter $addStoreFilter,
    ) {}

    /**
     * @return list<string>
     */
    public function execute(?int $storeId = null): array
    {
        $connection = $this->resourceConnection->getConnection('read');
        $table      = $this->resourceConnection->getTableName(Table\Content::TABLE);

        $query = $connection->select()->from(
            ['c' => $table],
            ['identifier'],
        );

        if ($storeId !== null) {
            $this->addStoreFilter->execute($query, $storeId);
        }

        /** @var array<array-key, string> $identifiers */
        $identifiers = $connection->fetchCol($query);

        Assertion::allString($identifiers);

        return array_values($identifiers);
    }
}
