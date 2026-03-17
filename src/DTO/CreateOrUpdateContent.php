<?php

declare(strict_types=1);

namespace Renttek\WellKnown\DTO;

class CreateOrUpdateContent
{
    public function __construct(
        public readonly Content $content,
        /**
         * @var list<int>
         */
        public readonly array $storeIds,
    ) {}
}
