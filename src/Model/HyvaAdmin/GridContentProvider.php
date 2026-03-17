<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model\HyvaAdmin;

use Hyva\Admin\Api\HyvaGridArrayProviderInterface;
use Renttek\WellKnown\Query\GetAllForGrid;

class GridContentProvider implements HyvaGridArrayProviderInterface
{
    public function __construct(
        private readonly GetAllForGrid $getAllForGrid,
    ) {}

    /**
     * @return list<array{content_id: int, identifier: string, type: string, store_ids: list<int>}>
     */
    public function getHyvaGridData(): array
    {
        return $this->getAllForGrid->execute();
    }
}
