<?php
/**
 *
 * Copyright © 2018 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ShoppingCartPro\Model\Config\Source;

class CartAddedStyle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => 'Show informed popup'],
            ['value' => '2', 'label' => 'Make product image fly to footer cart panel'],
            ['value' => '3', 'label' => 'Make product image fly to cart sidebar'],
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}