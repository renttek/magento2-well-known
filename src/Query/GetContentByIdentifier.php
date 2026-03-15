<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
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
            ->where(sprintf('c.%s = ?', Table\Content::FIELD_IDENTIFIER), $identifier)
            ->limit(1);

        if ($storeId !== null) {
            $this->addStoreFilter->execute($query, $storeId);
        }

        $content = $connection->fetchRow($query);

        if (!is_array($content)) {
            return null;
        }

        try {
            Assertion::keyExists($content, Table\Content::FIELD_ID);
            Assertion::string($content[Table\Content::FIELD_ID]);
            Assertion::numeric($content[Table\Content::FIELD_ID]);

            Assertion::keyExists($content, Table\Content::FIELD_IDENTIFIER);
            Assertion::string($content[Table\Content::FIELD_IDENTIFIER]);

            Assertion::keyExists($content, Table\Content::FIELD_CONTENT);
            Assertion::string($content[Table\Content::FIELD_CONTENT]);
        } catch (AssertionFailedException) {
            return null;
        }

        return DTO\Content::fromArray($content);
    }
}
