<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model;

use Renttek\WellKnown\DTO;

interface WellKnownProviderInterface
{
    public function provides(string $identifier, ?int $storeId = null): bool;

    public function getContent(string $identifier, ?int $storeId = null): ?DTO\Content;
}
