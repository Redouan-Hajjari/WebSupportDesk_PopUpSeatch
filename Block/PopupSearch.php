<?php

/**
 * @author Redouan Hajjari <redouanhajjari@gmail.com>
 */

declare(strict_types=1);

namespace WebSupportDesk\PopUpSeatch\Block;

use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use WebSupportDesk\PopUpSeatch\Model\Config as PopupConfig;

class PopupSearch extends Template
{
    public function __construct(
        Context $context,
        private readonly CatalogImageHelper $catalogImageHelper,
        private readonly PopupConfig $popupConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getSuggestUrl(): string
    {
        return $this->getUrl('wsdpopupsearch/index/suggest');
    }

    public function getPlaceholderImageUrl(): string
    {
        return $this->catalogImageHelper->getDefaultPlaceholderUrl('small_image');
    }

    public function isEnabled(): bool
    {
        return $this->popupConfig->isEnabled();
    }

    protected function _toHtml(): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}

