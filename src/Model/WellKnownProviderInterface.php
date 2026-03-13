<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model;

use Renttek\WellKnown\DTO;

interface WellKnownProviderInterface
{
    public function provides(string $identifier): bool;

    public function getContent(string $identifier): ?DTO\Content;
}
