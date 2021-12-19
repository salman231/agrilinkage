<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Plugin;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection ;
use Magento\Store\Model\StoreManagerInterface;

class CustomerEmailValidatePlugin 
{

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerCollection $customerCollection
    ) {
        $this->customerCollection = $customerCollection;
        $this->storeManager = $storeManager;
    }
    public function beforeIsEmailAvailable(\Magento\Customer\Model\AccountManagement $subject, $customerEmail)
    {
        if (is_numeric($customerEmail)) {
            $customerCollections = $this->getCustomerByPhone($customerEmail);
            foreach ($customerCollections as $customer) {
                $customerEmail = $customer->getEmail();
            }
        }
        return $customerEmail;
    }

    public function getCustomerByPhone($phone)
    {   
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }
}