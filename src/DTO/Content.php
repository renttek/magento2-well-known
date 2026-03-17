<?php

declare(strict_types=1);

namespace Renttek\WellKnown\DTO;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Renttek\WellKnown\Model\Table;

class Content
{
    public function __construct(
        public readonly int    $id,
        public readonly string $identifier,
        public readonly Type   $type,
        public readonly string $content,
    ) {}

    public static function fromArrayOrNull(array $content): ?self
    {
        try {
            Assertion::keyExists($content, Table\Content::FIELD_ID);
            Assertion::string($content[Table\Content::FIELD_ID]);
            Assertion::numeric($content[Table\Content::FIELD_ID]);

            Assertion::keyExists($content, Table\Content::FIELD_IDENTIFIER);
            Assertion::string($content[Table\Content::FIELD_IDENTIFIER]);

            Assertion::keyExists($content, Table\Content::FIELD_TYPE);
            Assertion::string($content[Table\Content::FIELD_TYPE]);

            Assertion::keyExists($content, Table\Content::FIELD_CONTENT);
            Assertion::string($content[Table\Content::FIELD_CONTENT]);
        } catch (AssertionFailedException) {
            return null;
        }

        return new self(
            id        : (int) $content[Table\Content::FIELD_ID],
            identifier: $content[Table\Content::FIELD_IDENTIFIER],
            type      : Type::fromString($content[Table\Content::FIELD_TYPE]),
            content   : $content[Table\Content::FIELD_CONTENT],
        );
    }
}
