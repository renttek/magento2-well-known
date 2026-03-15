<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Renttek\WellKnown\Model\WellKnownProviderPool;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly WellKnownProviderPool $providerPool,
        private readonly StoreManagerInterface $storeManager,
        private readonly ResultFactory $resultPageFactory,
    ) {}

    public function execute(): Result\Raw
    {
        $identifier = $this->request->getParam('identifier');
        if (!is_string($identifier) || $identifier === '') {
            return $this->renderContent('ERROR: no identifier provided', 400);
        }

        $provider = $this->providerPool->getProvider($identifier);
        if ($provider === null) {
            return $this->renderContent('ERROR: no provider found for identifier ' . $identifier, 500);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpCastIsUnnecessaryInspection */
        $storeId = (int) $this->storeManager->getStore()->getId();
        $content = $provider->getContent($identifier, $storeId)?->content;
        if ($content === null) {
            return $this->renderContent('ERROR: could not fetch content for identifier ' . $identifier, 500);
        }

        return $this->renderContent($content);
    }

    private function renderContent(string $content, int $statusCode = 200): Result\Raw
    {
        /** @var Result\Raw $result */
        $result = $this->resultPageFactory->create(ResultFactory::TYPE_RAW);

        $result->setHeader('Content-Type', 'text/plain');
        $result->setContents($content);
        $result->setHttpResponseCode($statusCode);

        return $result;
    }
}
