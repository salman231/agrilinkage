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

class Broker extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
                ['value' => 'Cornelia Parker', 'label' => __('Cornelia Parker')],
                ['value' => 'Sudane Nugent', 'label' => __('Sudane Nugent')],
                ['value' => 'Phillip Myrie', 'label' => __('Phillip Myrie')],
                ['value' => 'Oshane Thompson', 'label' => __('Oshane Thompson')]

            ];
        }
        return $this->_options;
    }
}