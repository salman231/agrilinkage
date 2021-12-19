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

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Sunbrains\SMSNotification\Model\SMSNotificationService;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class SmsOrderAfterSave
{
    /**  @var OrderRepositoryInterface */
    private $orderRepositoryInterface;

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**
     * Constructor
     * @param SmsNotificationService  $smsNotificationService
     * @param HelperData $dataHelper
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param OrderRepositoryInterface $orderRepositoryInterface
     */
    
    public function __construct(
        SMSNotificationService $smsNotificationService,
        HelperData $dataHelper,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        OrderRepositoryInterface $orderRepositoryInterface
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->datahelper = $dataHelper;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    /* @codingStandardsIgnoreStart */
    public function afterSave(\Magento\Sales\Api\OrderRepositoryInterface $orderRepo, $order)
    {
        /* @codingStandardsIgnoreEnd */
        $customerEvent = 'customer_neworder';
        $adminEvent = 'admin_new_order';
        $customerEventList  = $this->datahelper->getCustomerEvents();
        $adminEventList  = $this->datahelper->getAdminEvents();

        //if ($this->datahelper->getModuleStatus() && ($order->getState() == 'new' && $order->getStatus() == 'pending')) {
        if (($order->getState() == 'new' && $order->getStatus() == 'pending')) {
            if (in_array($customerEvent, $customerEventList)) {
                $this->sendOrderSmsToCustomer($order, $customerEvent);
            }
            if ($this->datahelper->getNotifyAdmin()) {
                if (in_array($adminEvent, $adminEventList)) {
                    $this->sendOrderSmsToAdmin($order, $adminEvent);
                }
            }
        }

        return $order;
    }

    /**  @return string */

    private function getToNumber($order)
    {
        if ($this->datahelper->geSelectedCustomerNumber() == 'shipping_add_no') {
            return  $order->getShippingAddress()->getTelephone();
        }

        if ($this->datahelper->geSelectedCustomerNumber() == 'billing_add_no') {
            return  $order->getBillingAddress()->getTelephone();
        }

        if ($this->datahelper->geSelectedCustomerNumber() == 'both') {
            $no = [$order->getShippingAddress()->getTelephone(), $order->getBillingAddress()->getTelephone()];
            return $no;
        }
    }

    /**  @return string */

    private function getApiVersion()
    {
        return  $this->datahelper->getApiGateway();
    }

    /**  @return string */

    private function getTransactionType()
    {
        return 'Order Suceess';
    }

    /**  @return string */

    private function getMessageText($order, $eventType)
    {
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $order->getStoreId());
        $_message = $sms->getData('template_content');
        $message = $this->datahelper->setOrderMesageText(
            $_message,
            $this->datahelper->getOrderedData($order)
        );

        return $message;
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

    public function sendOrderSmsToCustomer($order, $eventType)
    {
        if (is_array($this->getToNumber($order))) {
            $toNumber = $this->getToNumber($order);
            foreach ($toNumber as $toNumber) {
                $this->smsNotificationService->setToNumber($toNumber);
                $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
                $this->smsNotificationService->setTransactionType($eventType);
                $this->smsNotificationService->setApiVersion($this->getApiVersion());
                $this->callSmsSending();
            }
        } else {
            $this->smsNotificationService->setToNumber($this->getToNumber($order));
            $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
            $this->smsNotificationService->setTransactionType($eventType);
            $this->smsNotificationService->setApiVersion($this->getApiVersion());
            $this->callSmsSending();
        }
    }

    public function sendOrderSmsToAdmin($order, $eventType)
    {
        $toNumber = $this->datahelper->getAdminContactNumbers();
        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
            $this->smsNotificationService->setToNumber($toNumber);
            $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
            $this->smsNotificationService->setTransactionType($eventType);
            $this->smsNotificationService->setApiVersion($this->getApiVersion());
            $this->callSmsSending();
        }
    }
}
