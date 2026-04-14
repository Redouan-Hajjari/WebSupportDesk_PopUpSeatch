<?php

/**
 * @author Redouan Hajjari <redouanhajjari@gmail.com>
 */

declare(strict_types=1);

namespace WebSupportDesk\PopUpSeatch\Controller\Index;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Store\Model\StoreManagerInterface;

class Suggest implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly JsonFactory $jsonFactory,
        private readonly CollectionFactory $productCollectionFactory,
        private readonly Visibility $visibility,
        private readonly PricingHelper $pricingHelper,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function execute(): Json
    {
        $query = trim((string)$this->request->getParam('q', ''));
        $items = [];

        if (mb_strlen($query) >= 2) {
            $items = $this->getMatches($query);
        }

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'query' => $query,
            'items' => $items,
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getMatches(string $query): array
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setStoreId((int)$this->storeManager->getStore()->getId());
        $collection->addStoreFilter();
        $collection->addAttributeToSelect(['name', 'small_image', 'price']);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('visibility', ['in' => $this->visibility->getVisibleInSearchIds()]);
        $collection->addAttributeToFilter('name', ['like' => '%' . $query . '%']);
        $collection->setPageSize(8);
        $collection->setCurPage(1);

        $result = [];
        foreach ($collection as $product) {
            $image = '';
            $smallImage = (string)$product->getData('small_image');
            if ($smallImage !== '' && $smallImage !== 'no_selection') {
                $image = rtrim(
                    $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
                    '/'
                ) . '/catalog/product' . $smallImage;
            }

            $result[] = [
                'name' => (string)$product->getName(),
                'url' => (string)$product->getProductUrl(),
                'price' => (string)$this->pricingHelper->currency((float)$product->getFinalPrice(), true, false),
                'image' => $image,
            ];
        }

        return $result;
    }
}

