<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Stringable;

abstract class Content extends Action
{
    public const string ADMIN_RESOURCE = 'Renttek_WellKnown::manage';
    public const string MENU_ID        = 'Renttek_WellKnown::manage_content';

    /**
     * @param list<string|Stringable> $breadCrumbs
     */
    public function initPage(string|Stringable $title, array $breadCrumbs): Page
    {
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $page->setActiveMenu(self::MENU_ID);
        $page->getConfig()->getTitle()->prepend((string) $title);

        foreach ($breadCrumbs as $breadCrumb) {
            $page->addBreadcrumb((string) $breadCrumb, (string) $breadCrumb);
        }

        return $page;
    }
}
