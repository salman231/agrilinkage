<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\ThemeLayoutPro\Block;

use Magento\Customer\Model\Context;

class LayoutAbstract extends \Magento\Framework\View\Element\Template
{
    protected $_filterProvider;
    
    protected $_storeManager;
    
    protected $_blockFilter;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Codazon\ThemeLayoutPro\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $context->getStoreManager();
        $storeId = $this->_storeManager->getStore()->getId();
		$this->_blockFilter = $this->_filterProvider->getBlockFilter()->setStoreId($storeId);
    }
    
    public function filter($content){
		return $this->_blockFilter->filter($content);
	}
    
    public function getMediaUrl()
    {
        return $this->helper->getMediaUrl();
    }
}