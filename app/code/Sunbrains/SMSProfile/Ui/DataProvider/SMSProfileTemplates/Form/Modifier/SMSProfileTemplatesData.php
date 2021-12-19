<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Ui\DataProvider\SMSProfileTemplates\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class SMSProfileTemplatesData implements ModifierInterface
{
    
    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
    /**
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
