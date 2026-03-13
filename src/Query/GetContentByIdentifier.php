<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Query;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Magento\Framework\App\ResourceConnection;
use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Model\Table;

class GetContentByIdentifier
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
    ) {}

    public function execute(string $identifier): ?DTO\Content
    {
        $connection = $this->resourceConnection->getConnection('read');
        $table      = $this->resourceConnection->getTableName(Table\Content::TABLE);

        $query = $connection->select()
            ->from(['c' => $table])
            ->where('identifier = ?', $identifier);

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
