<?php

declare(strict_types=1);

namespace Renttek\WellKnown\DTO;

enum Type: string
{
    case Plain = 'plain';
    case Json  = 'json';
    case Xml   = 'xml';
    case Html  = 'html';

    public static function fromString(string $type): self
    {
        return self::tryFrom($type) ?? self::Plain;
    }
}
