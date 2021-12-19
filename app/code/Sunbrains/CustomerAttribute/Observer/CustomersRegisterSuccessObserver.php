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

namespace Sunbrains\CustomerAttribute\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;

/**
 * Webkul Marketplace CustomerRegisterSuccessObserver Observer.
 */
class CustomersRegisterSuccessObserver implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager,
     * @param CollectionFactory                           $collectionFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CollectionFactory $collectionFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_collectionFactory = $collectionFactory;
        $this->_date = $date;
    }

    /**
     * customer register event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer['account_controller'];
        try {

			
            $paramData = $data->getRequest()->getParams();
			if(isset($paramData['broker'])) {
				if($paramData['broker'] == 'Cornelia Parker'){
					$adminEmail = 'corneila.parker@rada.gov.jm';
				}

				if($paramData['broker'] == 'Oshane Thompson'){
					$adminEmail = 'oshane.thompson@rada.gov,jm';
				}

				if($paramData['broker'] == 'Phillip Myrie'){
					$adminEmail = 'phillip.myrie@rada.gov.jm';
				}

				if($paramData['broker'] == 'Sudane Nugent'){
					//echo "asdasd";exit;
					$adminEmail = 'sudane.nugent@rada.gov.jm';
				}
			}	


            $customer = $observer->getCustomer();

            $helper = $this->_objectManager->get(
                'Webkul\Marketplace\Helper\Data'
            );

            $adminStoremail = $helper->getAdminEmailId();
            $adminEmail = '';
			if($adminEmail == ''){
                $adminEmail = $adminStoremail ? $adminStoremail : $helper->getDefaultTransEmailId();
            }

            $adminUsername = $helper->getAdminName();
            $senderInfo = [
                'name' => $customer->getFirstName().' '.$customer->getLastName(),
                'email' => $customer->getEmail(),
            ];
            $receiverInfo = [
                'name' => $adminUsername,
                'email' => $adminEmail,
            ];
            $emailTemplateVariables['myvar1'] = $customer->getFirstName().' '.
                $customer->getLastName();
            $emailTemplateVariables['myvar2'] = $this->_objectManager->get(
                'Magento\Backend\Model\Url'
            )->getUrl(
                'customer/index/edit',
                ['id' => $customer->getId()]
            );
            $emailTemplateVariables['myvar3'] = $helper->getAdminName();

            $this->_objectManager->create(
                'Webkul\Marketplace\Helper\Email'
            )->sendNewSellerRequest(
                $emailTemplateVariables,
                $senderInfo,
                $receiverInfo
            );
        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }
}
