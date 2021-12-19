<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
namespace Sunbrains\SMSProfile\Model\Attribute\Backend;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class Mobile extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    public function __construct(
      \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Generate and set unique SKU to product
     *
     * @param Product $object
     * @return void
     */
    protected function checkUniquePhone($object)
    {
        $attribute = $this->getAttribute();
        $entity = $attribute->getEntity();
        $attributeValue = $object->getData($attribute->getAttributeCode());
        $increment = null;
        while (!$entity->checkAttributeUniqueValue($attribute, $object)) {
            throw new NoSuchEntityException(__('Account with mobile number already exist.'));
        }
    }

    /**
     * Make SKU unique before save
     *
     * @param Product $object
     * @return $this
     */
    public function beforeSave($object)
    {
        if ($this->moduleManager->isOutputEnabled('Sunbrains_SMSProfile')) {
            $this->checkUniquePhone($object);
        }    
        return parent::beforeSave($object);
    }
}
