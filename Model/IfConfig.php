<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Element119\IfConfigLayout\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Layout\Element;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class IfConfig
{
    public const LAYOUT_ATTRIBUTE_IFCONFIG = 'ifconfig';

    private ScopeConfigInterface $scopeConfig;
    private AppState $appState;
    private RequestInterface $request;
    private StoreManagerInterface $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        AppState $appState,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    public function isSetFlag(string $configPath): bool
    {
        return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function getStoreId(): int
    {
        $storeId = 0;

        if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML) {
            $storeId = $this->storeManager->getStore($this->request->getParam('store', 0))->getId();
        } elseif ($this->appState->getAreaCode() === Area::AREA_FRONTEND) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return (int)$storeId;
    }

    public function shouldRemoveElement(Element $element): bool
    {
        $configPath = $element->getAttribute(self::LAYOUT_ATTRIBUTE_IFCONFIG);
        $removeValue = filter_var($element->getAttribute('remove'), FILTER_VALIDATE_BOOLEAN);

        return (!$configPath || $removeValue)
            ? $removeValue
            : !$this->isSetFlag($configPath);
    }
}
