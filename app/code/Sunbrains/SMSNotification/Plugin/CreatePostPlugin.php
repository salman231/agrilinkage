<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category Sunbrains
 * @package Sunbrains_SMSNotification
 * @copyright Copyright (c) 2019 Mage Delight (http://www.sunbrains.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Sunbrains <info@sunbrains.com>
 */

namespace Sunbrains\SMSNotification\Plugin;

use Sunbrains\SMSNotification\Model\SMSNotificationService;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Url;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class CreatePostPlugin
{

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

       /**  @var Url */
    private $customerUrl;

    /**  @var StoreManagerInterface */
    private $storeManager;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**
     * Constructor
     * @param SMSNotificationService $smsNotificationService
     * @param Url $customerUrl
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param StoreManagerInterface $storeManager
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        Url $customerUrl,
        StoreManagerInterface $storeManager,
        HelperData $dataHelper
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->customerUrl = $customerUrl;
        $this->datahelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
    }

    public function afterExecute(\Magento\Customer\Controller\Account\CreatePost $subject, $result)
    {
        $data =  $subject->getRequest()->getPostValue();
        if($data['is_seller'] == 0){
            $adminEvent = 'admin_new_customer';
        } else {
            $adminEvent = 'admin_customer_contact';
        }

        //admin_customer_contact
        $adminEventList  = $this->datahelper->getAdminEvents();
        //if ($this->datahelper->getModuleStatus()) {
            if ($this->datahelper->getNotifyAdmin()) {
                if (in_array($adminEvent, $adminEventList)) {
                    $this->sendSmsToAdmin($data, $adminEvent);
                }
            }
        //}

        return $result;
    }

    private function getApiVersion()
    {
        return  $this->datahelper->getApiGateway();
    }
    
    /**  @return string */

    private function getTransactionType()
    {
        return 'Customer Registeration success';
    }

    public function callSmsSending()
    {
        if ($this->getApiVersion() == 'Twilio Api Service') {
            $this->smsNotificationService->sendSmsWithTwilio();
        } elseif ($this->getApiVersion() == 'BulkSms') {
            $this->smsNotificationService->sendSmsWithBulkSmsService();
        } else {
            $this->smsNotificationService->sendSmsViaOtherServices();
        }
    }

    private function setWelcomeMesageText($message, $data)
    {
        $keywords   = [
            '{name}',
            '{store}',
            '{username}',
            '{url}',
            '{broker}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    public function getCustomerLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    public function getCurrentStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function sendSmsToAdmin($data, $eventType)
    {
        $toNumber = $data['customer_mobile'];//$this->datahelper->getAdminContactNumbers();
        $_data = $this->getCustomerData($data);
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setWelcomeMesageText($_message, $_data);

        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
             $this->smsNotificationService->setToNumber($toNumber);
             $this->smsNotificationService->setMessageContent($message);
             $this->smsNotificationService->setTransactionType($eventType);
             $this->smsNotificationService->setApiVersion($this->getApiVersion());
             $this->callSmsSending();
        }
    }

    public function getCustomerData($data)
    {
        $_data = [
            'name' => $data['firstname'].' '.$data['lastname'],
            'store'=> $this->getCurrentStoreName(),
            'username' =>$data['email'],
            'url' =>  $this->getCustomerLoginUrl(),
            //'broker' => $data['broker']
        ];

        return $_data;
    }
}
