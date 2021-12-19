<?php
/**
 * *
 *  * Sunbrains
 *  *
 *  * @category    Sunbrains
 *  * @package     Sunbrains_Loginhistory
 *  * @copyright   Copyright (c) Sunbrains
 *  *
 *
 */

namespace Sunbrains\CustomerAttribute\Model\Customer\Attribute\Source;

class Paris extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                    ['value' => 'Trelawny', 'label' => __('Trelawny')],
                    ['value' => 'Saint Catherine', 'label' => __('Saint Catherine')],
                    ['value' => 'Saint James', 'label' => __('Saint James')],
                    ['value' => 'Westmoreland', 'label' => __('Westmoreland')],
                    ['value' => 'Saint Thomas', 'label' => __('Saint Thomas')],
                    ['value' => 'Portland', 'label' => __('Portland')],
                    ['value' => 'Saint Elizabeth', 'label' => __('Saint Elizabeth')],
                    ['value' => 'Saint Mary', 'label' => __('Saint Mary')],
                    ['value' => 'Saint Andrew', 'label' => __('Saint Andrew')],
                    ['value' => 'Clarendon', 'label' => __('Clarendon')],
                    ['value' => 'Hanover', 'label' => __('Hanover')],
                    ['value' => 'Manchester', 'label' => __('Manchester')],
                    ['value' => 'Saint Ann', 'label' => __('Saint Ann')],
                    ['value' => 'Kingston', 'label' => __('Kingston')],

            ];
        }

        return $this->_options;
    }
	
	public function getOptionText($value) 
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
