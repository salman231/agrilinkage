<?php
/**
 *
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ThemeLayoutPro\Model\Config\Source;

class ListingStyles implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    
    public function toOptionArray()
    {
        $options = [
            ['value' => 'product/list/list-styles/list-style01.phtml', 'label' => __('Style 01')],
            ['value' => 'product/list/list-styles/list-style02.phtml', 'label' => __('Style 02')],
			['value' => 'product/list/list-styles/list-style03.phtml', 'label' => __('Style 03')],
			['value' => 'product/list/list-styles/list-style04.phtml', 'label' => __('Style 04')],
			['value' => 'product/list/list-styles/list-style05.phtml', 'label' => __('Style 05')],
			//['value' => 'product/list/list-styles/list-style06.phtml', 'label' => __('Style 06')],
			['value' => 'product/list/list-styles/list-style09.phtml', 'label' => __('Style 09')],
			['value' => 'product/list/list-styles/list-style10.phtml', 'label' => __('Style 10')],
			['value' => 'product/list/list-styles/list-style13.phtml', 'label' => __('Style 13')],
			['value' => 'product/list/list-styles/list-style14.phtml', 'label' => __('Style 14')],
			['value' => 'product/list/list-styles/list-style16.phtml', 'label' => __('Style 16')],
			['value' => 'product/list/list-styles/list-style17.phtml', 'label' => __('Style 17')],
			['value' => 'product/list/list-styles/list-style18.phtml', 'label' => __('Style 18')],
			['value' => 'product/list/list-styles/list-style19.phtml', 'label' => __('Style 19')],
			['value' => 'product/list/list-styles/list-style20.phtml', 'label' => __('Style 20')]
        ];
        return $options;
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
