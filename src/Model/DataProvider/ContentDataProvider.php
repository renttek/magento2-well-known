<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Model\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Override;
use Renttek\WellKnown\Query\GetContentById;

class ContentDataProvider extends AbstractDataProvider
{
    /**
     * @var array<int, array{
     *     content_id: int|null,
     *     identifier: string,
     *     type: string,
     *     content: string
     * }>
     */
    private array $loadedData;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly GetContentById   $getContentById,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array                             $meta = [],
        array                             $data = [],
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    #[\Override]
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $contentId = $this->request->getParam($this->getRequestFieldName());
        if (!is_string($contentId) || !ctype_digit($contentId)) {
            return [];
        }

        $contentId = (int) $contentId;
        $content   = $this->getContentById->execute($contentId);
        if ($content === null) {
            return [];
        }

        $this->loadedData[$contentId] = [
            'content_id' => $content->id,
            'identifier' => $content->identifier,
            'type'       => $content->type->value,
            'content'    => $content->content,
        ];

        return $this->loadedData;
    }

    #[Override]
    public function addFilter(Filter $filter): void
    {
        // override, to not depend on collection use, that parent requires
    }
}
