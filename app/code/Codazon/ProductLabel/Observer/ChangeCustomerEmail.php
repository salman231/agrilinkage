<?php
namespace Codazon\ProductLabel\Observer;

use Magento\Framework\Event\ObserverInterface;

class ChangeCustomerEmail implements ObserverInterface
{
    protected $_customerRepositoryInterface;
	
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
		if(!$customer->getEmail()){
			$email = "noreply@".$customer->getId()."-agrilinkages.com";
			$customer->setEmail($email);
			$this->_customerRepositoryInterface->save($customer);
		}
    }
}