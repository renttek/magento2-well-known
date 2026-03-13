<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model;

use function array_find;

class WellKnownProviderPool
{
    public function __construct(
        /**
         * @var list<WellKnownProviderInterface> $providers
         */
        private readonly array $providers,
    ) {}

    public function provides(string $identifier): bool
    {
        return $this->getProvider($identifier) !== null;
    }

    public function getProvider(string $identifier): ?WellKnownProviderInterface
    {
        return array_find(
            $this->providers,
            static fn(WellKnownProviderInterface $p): bool => $p->provides($identifier),
        );
    }
}
