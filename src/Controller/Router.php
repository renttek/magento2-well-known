<?php

declare(strict_types=1);

namespace Renttek\WellKnown\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Route\ConfigInterface;
use Magento\Framework\App\Router\ActionList;
use Magento\Framework\App\RouterInterface;
use ReflectionException;
use Renttek\WellKnown\Model\Config;
use Renttek\WellKnown\Model\WellKnownProviderPool;

class Router implements RouterInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly WellKnownProviderPool $providerPool,
        private readonly ConfigInterface $routeConfig,
        private readonly ActionFactory $actionFactory,
        private readonly ActionList $actionList,
    ) {}

    public function match(RequestInterface $request): ?ActionInterface
    {
        if (!$request instanceof Http) {
            return null;
        }

        if (!$this->config->isEnabled()) {
            return null;
        }

        $path = trim($request->getPathInfo(), '/');
        if (!$this->isWellKnownPath($path)) {
            return null;
        }

        $identifier = $this->getIdentifier($path);
        if ($this->isExcludedIdentifier($identifier)) {
            return null;
        }

        if (!$this->providerPool->provides($identifier)) {
            return null;
        }

        $request->setParams(['identifier' => $identifier] + $request->getParams());

        return $this->createAction();
    }

    private function createAction(): ?ActionInterface
    {
        $modules = $this->routeConfig->getModulesByFrontName('renttekwellknown');
        if ($modules === []) {
            return null;
        }

        try {
            $actionClassName = $this->actionList->get($modules[0], null, 'index', 'index');
        } catch (ReflectionException) {
            return null;
        }

        return $actionClassName !== null
            ? $this->actionFactory->create($actionClassName)
            : null;
    }

    private function isExcludedIdentifier(string $identifier): bool
    {
        return in_array($identifier, $this->config->getExcludedPaths(), true);
    }

    private function isWellKnownPath(string $path): bool
    {
        return str_starts_with($path, '.well-known/');
    }

    private function getIdentifier(string $path): string
    {
        return substr($path, strlen('.well-known/'));
    }
}
