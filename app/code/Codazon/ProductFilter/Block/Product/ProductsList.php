<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Codazon\ProductFilter\Block\Product;
/**
 * Catalog Products List widget block
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    protected $urlHelper;
    protected $productCollectionFactory;
    protected $bestSellerCollectionFactory;
    protected $imageHelperFactory;
    protected $_filterData = [];
    protected $_sliderData = [];
    protected $_show = [];
    protected $_listProduct;
    protected $objectManager;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Codazon\ProductFilter\Model\ResourceModel\Bestsellers\CollectionFactory $bestSellerCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Codazon\ProductFilter\Block\ImageBuilderFactory $customImageBuilderFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->bestSellerCollectionFactory = $bestSellerCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->urlHelper = $urlHelper;
        $this->conditionsHelper = $conditionsHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->customImageBuilderFactory = $customImageBuilderFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_listProduct = $this->objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
        
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
    }
    
    public function getCacheKeyInfo()
    {
        if (!$this->hasData('cache_key_info')) {
            $cacheKeyInfo = [
                'PRODUCT_FILTER_WIDGET',
                $this->_storeManager->getStore()->getId(),
                $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
                $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
                intval($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1)),
                $this->objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
                $this->getData()
            ];
            $this->setData('cache_key_info', [md5(json_encode($cacheKeyInfo))]);
        }
        return $this->getData('cache_key_info');
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }
    
    public function getProductListBlock()
    {
        return $this->objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
    }
    
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product, $additional = [])
    {
        $url =  $this->_listProduct->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    
    protected function _getBestSellerProductCollection()
    {
        $collection = $this->bestSellerCollectionFactory->create();
        $bestSellerTable = $collection->getTable('sales_bestsellers_aggregated_daily');
        $collection->getSelect()->join(array('r' => $bestSellerTable), 'r.product_id=e.entity_id', array('*'))->group('e.entity_id');
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }
    
    protected function _getBestSellingCollection()
    {
        $orderItemCol = $this->objectManager->get('Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory')->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('parent_item_id', array('null' => true));
        $orderItemCol->getSelect()
            ->columns(array('ordered_qty' => 'SUM(`main_table`.`qty_ordered`)'))
            ->group('main_table.product_id')
            ->joinInner(
                array('sfo' => $orderItemCol->getTable('sales_order')),
                "(main_table.order_id = sfo.entity_id) AND (sfo.state <> 'canceled')",
                []
            );
        $collection = $this->_getAllProductProductCollection();
        $collection->getSelect()
            ->joinLeft(
                array('sfoi' => $orderItemCol->getSelect()),
                'e.entity_id = sfoi.product_id',
                array('ordered_qty' => 'sfoi.ordered_qty')
            )
            ->where('sfoi.ordered_qty > 0')
            ->order('ordered_qty desc');
        return $collection;
    }
    
    protected function _getAllProductProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1));

        if ($productIds = $this->getData('product_ids')) {
            if (!is_array($productIds)) {
                $productIds = explode(',', $productIds);
            }
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }
        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        //$this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }

    public function createCollection()
    {
        $isAjax = !($this->getData('ajax_load'));
        $collection = null;
        if ($isAjax) {
            $displayType = $this->getDisplayType();
            switch ($displayType) {
                case 'all_products':
                    $collection = $this->_getAllProductProductCollection();
                    break;
                case 'bestseller_products':
                    $collection = $this->_getBestSellingCollection();
                    break;
            }
            $sort = explode(' ', $this->getData('order_by'));
            $collection->addAttributeToSort($sort[0], $sort[1]);

            $this->_eventManager->dispatch(
                'catalog_block_product_list_collection',
                ['collection' => $collection]
            );
        }
        return $collection;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    public function subString($str, $strLenght)
    {
        $str = $this->stripTags($str);
        if(strlen($str) > $strLenght) {
            $strCutTitle = substr($str, 0, $strLenght);
            $str = substr($strCutTitle, 0, strrpos($strCutTitle, ' '))."&hellip;";
        }
        return $str;
    }

    public function getTemplate()
    {
        $isAjax = !($this->getData('ajax_load'));
        if ($isAjax){
            $template = $this->getData('filter_template');
            if($template == 'custom') {
                return $this->getData('custom_template');
            } else {
                return $template;
            }
		} else {
            return 'Codazon_ProductFilter::ajax/first_load.phtml';
		}
    }
    
    public function getElementShow()
    {
        if (!$this->_show) {
            $this->_show = explode(',', $this->getData('show'));
        }
        return $this->_show;
    }
    
    public function isShow($item)
    {
    	return in_array($item, $this->getElementShow());
    }
    
    public function getImage($product, $imageId, $attributes = [])
    {
        $width = $this->getData('thumb_width');
        $height = $this->getData('thumb_height');
        $attributes = array('resize_width' => $width, 'resize_height' => $height);

        $imageBuilder = $this->customImageBuilderFactory->create();
        return $imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
        return $html;
    }
    
    public function getBlockId()
    {
    	return uniqid("cdz_block_");
    }
    
    public function getFilterData()
    {
        if (!$this->_filterData) {
            $this->_filterData = [
                'is_ajax'               =>  1,
                'title'                 =>  $this->getData('title'),
                'display_type'          =>  $this->getData('display_type'),
                'products_count'        =>  $this->getData('products_count'),
                'order_by'              =>  $this->getData('order_by'),
                'show'                  =>  $this->getData('show'),
                'thumb_width'           =>  $this->getData('thumb_width'),
                'thumb_height'          =>  $this->getData('thumb_height'),
                'filter_template'       =>  $this->getData('filter_template'),
                'custom_template'       =>  $this->getData('custom_template'),
                'show_slider'           =>  $this->getData('show_slider'),
                'conditions_encoded'    =>  $this->getData('conditions_encoded'),
                'slider_nav'            => (int)$this->getData('slider_nav'),
                'slider_dots'           => (int)$this->getData('slider_dots'),
                'total_cols'            => (int)$this->getData('total_cols'),
                'total_rows'            => (int)$this->getData('total_rows'),
                'slider_margin'         => (int)$this->getData('slider_margin'),
                'cache_lifetime'        => $this->getData('cache_lifetime'),
                'product_ids'           => $this->getData('product_ids'),
                'cache_key_info'        => $this->getCacheKeyInfo(),
            ];
            $adapts = array('1900', '1600', '1420', '1280','980','768','480','320','0');
            foreach ($adapts as $adapt) {
                $this->_filterData['items_' . $adapt] = (float)$this->getData('items_' . $adapt);
            }
        }
        return $this->_filterData;
    }
    
    public function getSliderData() {
        if (!$this->_sliderData) {
            $this->_sliderData = [
                'nav'  => (bool)$this->getData('slider_nav'),
                'dots' => (bool)$this->getData('slider_dots')
            ];
            $adapts = array('1900', '1600', '1420', '1280','980','768','480','320','0');
            foreach ($adapts as $adapt) {
                 $this->_sliderData['responsive'][$adapt] = ['items' => (float)$this->getData('items_' . $adapt)];
            }
            $this->_sliderData['margin'] = (float)$this->getData('slider_margin');
        }
        return $this->_sliderData;
    }
    
    public function getProductDefaultQty($product)
    {
        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }
    
    public function getQuantityValidators()
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }
    
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            if (get_class($renderer) == 'Magento\Swatches\Block\Product\Renderer\Listing\Configurable\Interceptor') {
                return '';
            }
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }
}
