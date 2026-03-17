<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Renttek\WellKnown\DTO;
use Renttek\WellKnown\Model\WellKnownProviderPool;

class Index implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface      $request,
        private readonly WellKnownProviderPool $providerPool,
        private readonly StoreManagerInterface $storeManager,
        private readonly ResultFactory         $resultPageFactory,
    ) {}

    public function execute(): Result\Raw
    {
        $identifier = $this->request->getParam('identifier');
        if (!is_string($identifier) || $identifier === '') {
            return $this->renderError('ERROR: no identifier provided', 400);
        }

        $provider = $this->providerPool->getProvider($identifier);
        if ($provider === null) {
            return $this->renderError('ERROR: no provider found for identifier ' . $identifier, 500);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpCastIsUnnecessaryInspection */
        $storeId = (int) $this->storeManager->getStore()->getId();
        $content = $provider->getContent($identifier, $storeId);
        if ($content?->content === null) {
            return $this->renderError('ERROR: could not fetch content for identifier ' . $identifier, 500);
        }

        return $this->renderContent($content);
    }

    private function renderContent(DTO\Content $content): Result\Raw
    {
        /** @var Result\Raw $result */
        $result = $this->resultPageFactory->create(ResultFactory::TYPE_RAW);

        $result->setHeader('Content-Type', $this->getContentType($content));
        $result->setContents($content->content);
        $result->setHttpResponseCode(200);

        return $result;
    }

    private function renderError(string $message, int $statusCode): Result\Raw
    {
        /** @var Result\Raw $result */
        $result = $this->resultPageFactory->create(ResultFactory::TYPE_RAW);

        $result->setHeader('Content-Type', 'text/plain');
        $result->setContents($message);
        $result->setHttpResponseCode($statusCode);

        return $result;
    }

    private function getContentType(DTO\Content $content): string
    {
        return match ($content->type) {
            DTO\Type::Html => 'text/html',
            DTO\Type::Json => 'application/json',
            DTO\Type::Xml  => 'application/xml',
            default        => 'text/plain',
        };
    }
}
