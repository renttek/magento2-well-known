<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Adminhtml\Content;

use Renttek\WellKnown\Controller\Adminhtml\Content;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Content
{
    public function __construct(
        Context                      $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        /** @var Page $page */
        $page = $this->pageFactory->create();

        $page->addBreadcrumb(__('/.well-known/'), __('/.well-known/'))
            ->addBreadcrumb(__('Content'), __('Content'));

        $page->getConfig()->getTitle()->prepend(__('Manage /.well-known/ content'));

        return $page;
    }

}
