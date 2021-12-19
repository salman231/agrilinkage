<?php
/**
 * Copyright © 2018 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ShoppingCartPro\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class Crosssell extends \Magento\Framework\DataObject implements SectionSourceInterface
{
	protected $layoutFactory;
	
    protected $checkoutSession;
    
    protected $quote;
    
    /**
     * {@inheritdoc}
     */
	
    public function __construct(
		\Magento\Framework\View\Result\LayoutFactory $layoutFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($data);
		$this->layoutFactory = $layoutFactory;
        $this->checkoutSession = $checkoutSession;
    }
    
    public function getSectionData()
    {
		$layout = $this->layoutFactory->create(true);
        $layout->addHandle(['minicart_crosssell']);
        $items = [];
        foreach (array_reverse($this->getAllQuoteItems()) as $item) {
            $items[] = [
                'item_id'    => $item->getData('item_id'),
                'created_at'    => $item->getData('created_at'),
                'updated_at'    => $item->getData('updated_at')
            ];
        }
        return [
            'html'      => $layout->getLayout()->getOutput(),
            'items'  => $items
        ];
    }
    
    
    protected function getAllQuoteItems()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote()->getAllVisibleItems();
        }
        return $this->getQuote()->getAllVisibleItems();
    }
    
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }
}