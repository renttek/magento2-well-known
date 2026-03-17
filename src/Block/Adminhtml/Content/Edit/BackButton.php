<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Block\Adminhtml\Content\Edit;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    public function __construct(
        private readonly UrlInterface $url,
    ) {}

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $backUrl = $this->url->getUrl('*/*/index');
        return [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", $backUrl),
            'class'      => 'back',
            'sort_order' => 10,
        ];
    }
}
