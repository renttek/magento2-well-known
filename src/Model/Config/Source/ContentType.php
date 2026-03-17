<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Renttek\WellKnown\DTO;

class ContentType implements OptionSourceInterface
{
    /**
     * @return list<array{label: string, value: string}>
     */
    public function toOptionArray(): array
    {
        return array_map(
            static fn(DTO\Type $t): array => ['label' => $t->name, 'value' => $t->value],
            DTO\Type::cases(),
        );
    }
}
