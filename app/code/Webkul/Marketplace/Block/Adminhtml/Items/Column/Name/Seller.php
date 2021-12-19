<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Adminhtml\Items\Column\Name;

class Seller extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * Get Seller Name.
     *
     * @param string | $id
     *
     * @return array
     */
    public function getUserInfo($id)
    {
        $sellerId = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $this->getOrder();
        $orderId = $order->getId();
        $marketplaceSalesCollection = $objectManager->get(
            'Webkul\Marketplace\Model\Saleslist'
        )
        ->getCollection()
        ->addFieldToFilter(
            'mageproduct_id',
            ['eq' => $id]
        )
        ->addFieldToFilter(
            'order_id',
            ['eq' => $orderId]
        );
        if (count($marketplaceSalesCollection)) {
            foreach ($marketplaceSalesCollection as $mpSales) {
                $sellerId = $mpSales->getSellerId();
            }
        }
        if ($sellerId > 0) {
            $customer = $objectManager->get(
                'Magento\Customer\Model\Customer'
            )->load($sellerId);
            if ($customer) {
                $returnArray = [];
                $returnArray['name'] = $customer->getName();
                $returnArray['id'] = $sellerId;

                return $returnArray;
            }
        }
    }

    /**
     * Get Customer Url By Customer Id.
     *
     * @param string | $customerId
     *
     * @return string
     */
    public function getCustomerUrl($customerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlbuilder = $objectManager->get(
            'Magento\Framework\UrlInterface'
        );

        return $urlbuilder->getUrl(
            'customer/index/edit',
            ['id' => $customerId]
        );
    }
}
