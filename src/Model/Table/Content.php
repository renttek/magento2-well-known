<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model\Table;

/**
 * @internal
 */
class Content
{
    public const string TABLE = 'renttek_wellknown_content';

    public const string FIELD_ID         = 'content_id';
    public const string FIELD_IDENTIFIER = 'identifier';
    public const string FIELD_TYPE       = 'type';
    public const string FIELD_CONTENT    = 'content';

    public const string JOIN_STORE_IDS   = 'store_ids';
}
