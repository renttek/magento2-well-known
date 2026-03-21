<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Adminhtml\Content;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Renttek\WellKnown\Command\CreateOrUpdateContent;
use Renttek\WellKnown\Controller\Adminhtml\Content;
use Renttek\WellKnown\DTO;

class Save extends Content
{
    public function __construct(
        private readonly CreateOrUpdateContent $createOrUpdateContent,
        Context $context,
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        try {
            $content = $this->getContent();
            $this->createOrUpdateContent->execute($content);
        } catch (Exception) {
        }

        return $this->resultRedirectFactory
            ->create()
            ->setPath('*/*/index');
    }

    private function getContent(): DTO\Content
    {
        [
            'content_id' => $contentId,
            'identifier' => $identifier,
            'type'       => $type,
            'content'    => $content,
            'store_ids'  => $storeIds,
        ] = $this->getRequest()->getParams();

        /** @var list<string|int>|string|null $storeIds */
        if (!is_array($storeIds)) {
            $storeIds = [];
        }

        $storeIds = array_map(intval(...), $storeIds);

        $contentId = ctype_digit((string) $contentId)
            ? (int) $contentId
            : null;

        return new DTO\Content(
            $contentId,
            $identifier,
            DTO\Type::fromString($type),
            $content,
            $storeIds,
        );
    }
}
