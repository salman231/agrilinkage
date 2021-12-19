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
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;
use Sunbrains\SMSNotification\Helper\Data as HelperData;

class ContactPostPlugin
{

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**
     * Constructor
     * @param SMSNotificationService $smsNotificationService
     * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        HelperData $dataHelper
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->datahelper = $dataHelper;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
    }
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {
        $data =  $subject->getRequest()->getPostValue();
        $customerEvent = 'customer_contact';
        $adminEvent = 'admin_customer_contact';
        $customerEventList  = $this->datahelper->getCustomerEvents();
        $adminEventList  = $this->datahelper->getAdminEvents();
        if ($this->datahelper->getModuleStatus()) {
            if (in_array($customerEvent, $customerEventList)) {
                $this->sendContactSmsToCustomer($data, $customerEvent);
            }
            if ($this->datahelper->getNotifyAdmin()) {
                if (in_array($adminEvent, $adminEventList)) {
                    $this->sendContactSmsToAdmin($data, $adminEvent);
                }
            }
        }

        return $result;
    }

    private function getApiVersion()
    {
        return  $this->datahelper->getApiGateway();
    }
    
    /**  @return string */

    private function getTransactionType()
    {
        return 'Contact Us Sms';
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

    private function setContactMesageText($message, $data)
    {
        $keywords   = [
            '{name}',
            '{comment}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    private function sendContactSmsToCustomer($data, $eventType)
    {
        $toNumber = $data['telephone'];
        $_data = [
            'name' => $data['name'],
            'comment'=>$data['comment'],
        ];
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setContactMesageText($_message, $_data);
        $this->smsNotificationService->setToNumber($toNumber);
        $this->smsNotificationService->setMessageContent($message);
        $this->smsNotificationService->setTransactionType($eventType);
        $this->smsNotificationService->setApiVersion($this->getApiVersion());
        $this->callSmsSending();
    }

    private function sendContactSmsToAdmin($data, $eventType)
    {
        $toNumber = $this->datahelper->getAdminContactNumbers();
        $_data = [
            'name' => $data['name'],
            'comment'=>$data['comment'],
        ];
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setContactMesageText($_message, $_data);
        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
             $this->smsNotificationService->setToNumber($toNumber);
             $this->smsNotificationService->setMessageContent($message);
             $this->smsNotificationService->setTransactionType($eventType);
             $this->smsNotificationService->setApiVersion($this->getApiVersion());
             $this->callSmsSending();
        }
    }
}
