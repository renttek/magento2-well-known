<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model;

use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Query\GetAllIdentifiers;
use Renttek\WellKnown\Query\GetContentByIdentifier;

class DatabaseProvider implements WellKnownProviderInterface
{
    public function __construct(
        private readonly GetAllIdentifiers      $getAllIdentifiers,
        private readonly GetContentByIdentifier $getContentByIdentifier,
    ) {}

    public function provides(string $identifier): bool
    {
        return in_array(
            $identifier,
            $this->getAllIdentifiers->execute(),
            true,
        );
    }

    public function getContent(string $identifier): ?DTO\Content
    {
        return $this->getContentByIdentifier->execute($identifier);
    }
}
