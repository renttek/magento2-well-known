<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Adminhtml\Content;

use Renttek\WellKnown\Controller\Adminhtml\Content;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Content
{
    public function execute(): Page
    {
        return $this->initPage(
            __('Edit Content'),
            [
                __('/.well-known/'),
                __('Content'),
                __('Edit'),
            ],
        );
    }
}
