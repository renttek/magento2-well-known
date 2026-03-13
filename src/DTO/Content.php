<?php

declare(strict_types=1);

namespace Renttek\WellKnown\DTO;

use Renttek\WellKnown\Model\Table;

class Content
{
    public function __construct(
        public readonly int    $id,
        public readonly string $identifier,
        public readonly string $content,
    ) {}

    /**
     * @param array{content_id: int|string, identifier: string, content: string} $content
     */
    public static function fromArray(array $content): self
    {
        return new self(
            id        : (int) $content[Table\Content::FIELD_ID],
            identifier: $content[Table\Content::FIELD_IDENTIFIER],
            content   : $content[Table\Content::FIELD_CONTENT],
        );
    }
}
