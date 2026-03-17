<?php

declare(strict_types=1);

namespace Renttek\WellKnown\DTO;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Renttek\WellKnown\Model\Table;

class Content
{
    public function __construct(
        public readonly ?int   $id,
        public readonly string $identifier,
        public readonly Type   $type,
        public readonly string $content,
    ) {}

    public static function fromArrayOrNull(array $data): ?self
    {
        try {
            $contentId = $data[Table\Content::FIELD_ID] ?? null;
            Assertion::nullOrDigit($contentId);
            $contentId = $contentId !== null
                ? (int) $contentId
                : null;

            $identifier = $data[Table\Content::FIELD_IDENTIFIER] ?? null;
            Assertion::string($identifier);
            Assertion::notBlank($identifier);

            $type = $data[Table\Content::FIELD_TYPE] ?? null;
            Assertion::string($type);
            Assertion::notBlank($type);
            $type = Type::fromString($type);

            $content = $data[Table\Content::FIELD_CONTENT] ?? null;
            Assertion::string($content);
        } catch (AssertionFailedException) {
            return null;
        }

        return new self(
            id        : $contentId,
            identifier: $identifier,
            type      : $type,
            content   : $content,
        );
    }
}
