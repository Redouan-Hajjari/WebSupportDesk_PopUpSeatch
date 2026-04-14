<?php

/**
 * @author Redouan Hajjari <redouanhajjari@gmail.com>
 */

declare(strict_types=1);

namespace WebSupportDesk\PopUpSeatch\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    public const XML_PATH_ENABLED = 'websupportdesk_popupseatch/general/enabled';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        if ($storeId === null) {
            try {
                $storeId = (int)$this->storeManager->getStore()->getId();
            } catch (\Throwable) {
                $storeId = 0;
            }
        }

        if ($storeId > 0) {
            return $this->scopeConfig->isSetFlag(
                self::XML_PATH_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}

