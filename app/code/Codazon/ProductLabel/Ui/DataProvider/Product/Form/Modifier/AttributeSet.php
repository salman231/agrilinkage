<?php
namespace Codazon\ProductLabel\Ui\DataProvider\Product\Form\Modifier;
/**
 * Add "Attribute Set" to first fieldset
 */
class AttributeSet extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AttributeSet
{
    public function modifyMeta(array $meta)
    {
		$meta = parent::modifyMeta($meta);
      
		unset($meta['product-details']['children']['container_manufacturer']);
		unset($meta['product-details']['children']['container_ts_dimensions_length']);
		unset($meta['product-details']['children']['container_ts_dimensions_width']);
		unset($meta['product-details']['children']['container_ts_dimensions_height']);
		unset($meta['product-details']['children']['container_country_of_manufacture']);
		unset($meta['product-details']['children']['container_mp_product_cart_limit']);
		//unset($meta['product-details']['children']['attribute_set_id']);
        return $meta;
    }
}
