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
            $dto = $this->getCreateOrUpdateDto();
            $this->createOrUpdateContent->execute($dto);
        } catch (Exception) {
        }

        return $this->resultRedirectFactory
            ->create()
            ->setPath('*/*/index');
    }

    private function getCreateOrUpdateDto(): DTO\CreateOrUpdateContent
    {
        [
            'content_id' => $contentId,
            'identifier' => $identifier,
            'type'       => $type,
            'content'    => $content,
        ] = $this->getRequest()->getParams();

        $contentId = ctype_digit((string) $contentId)
            ? (int) $contentId
            : null;

        return new DTO\CreateOrUpdateContent(
            new DTO\Content(
                $contentId,
                $identifier,
                DTO\Type::fromString($type),
                $content,
            ),
            [],
        );
    }
}
