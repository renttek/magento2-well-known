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

    public function execute(DTO\CreateOrUpdateContent $createOrUpdateContent): void
    {
        $connection = $this->resourceConnection->getConnection('write');

        // TODO: assign stores
        // TODO: check collision with store assignment

        $connection->insertOnDuplicate(
            table : Table\Content::TABLE,
            data  : [
                Table\Content::FIELD_ID         => $createOrUpdateContent->content->id,
                Table\Content::FIELD_IDENTIFIER => $createOrUpdateContent->content->identifier,
                Table\Content::FIELD_TYPE       => $createOrUpdateContent->content->type->value,
                Table\Content::FIELD_CONTENT    => $createOrUpdateContent->content->content,
            ],
            fields: [
                Table\Content::FIELD_IDENTIFIER,
                Table\Content::FIELD_TYPE,
                Table\Content::FIELD_CONTENT,
            ],
        );
    }
}
