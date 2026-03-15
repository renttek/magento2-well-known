<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

abstract class Content extends Action
{
    public const string ADMIN_RESOURCE = 'Renttek_WellKnown::manage';
    public const string MENU_ID        = 'Renttek_WellKnown::manage_content';

    public function initPage(Page $resultPage): void
    {
        $resultPage->setActiveMenu(self::MENU_ID);
    }
}
