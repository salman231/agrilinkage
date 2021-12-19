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
 
namespace Sunbrains\SMSNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as Observer;
use Magento\Sales\Model\OrderRepository;
use Sunbrains\SMSNotification\Model\SMSNotificationService;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class ShipmentTrackSave implements ObserverInterface
{
     /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**  @var OrderRepository */
    private $orderRepository;
    
    /**
     * Constructor
     * @param OrderRepository  $orderRepository
     * @param SMSNotificationService $smsNotificationService
     * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
     * @param HelperData $dataHelper
     */

    public function __construct(
        OrderRepository $orderRepository,
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        HelperData $dataHelper
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->datahelper = $dataHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * The execute class
     * @param Observer $observer
     * @return void
     */
    
    public function execute(Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();
        if ($track->getOrderId()) {
            $orderId = $track->getOrderId();
            $trackTitle = $track->getTitle();
            $trackNumber = $track->getTrackNumber();
            $order = $this->getOrderById($orderId);

            $data['order_id'] =$order->getIncrementId();
            $data['trackingtitle'] =$trackTitle;
            $data['tracknumber'] =$trackNumber;

            $customerEvent = 'customer_shipment_tracking';
            $customerEventList  = $this->datahelper->getCustomerEvents();
            if ($this->datahelper->getModuleStatus() && (in_array($customerEvent, $customerEventList))) {
                $sms = $this->smsTemplatesRepository->getByEventType($customerEvent, $order->getStoreId());
                $_message = $sms->getData('template_content');
                $message =$this->setTrackingMesageText($_message, $data);
                if (is_array($this->getToNumber($order))) {
                    foreach ($this->getToNumber($order) as $toNumber) {
                        $this->smsNotificationService->setToNumber($toNumber);
                        $this->smsNotificationService->setMessageContent($message);
                        $this->smsNotificationService->setTransactionType($customerEvent);
                        $this->smsNotificationService->setApiVersion($this->getApiVersion());
                        $this->callSmsSending();
                    }
                } else {
                    $this->smsNotificationService->setToNumber($this->getToNumber($order));
                    $this->smsNotificationService->setMessageContent($message);
                    $this->smsNotificationService->setTransactionType($customerEvent);
                    $this->smsNotificationService->setApiVersion($this->getApiVersion());
                    $this->callSmsSending();
                }
            }
        }
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
        return 'Shipment Created';
    }
    
    private function callSmsSending()
    {
        if ($this->getApiVersion() == 'Twilio Api Service') {
            $this->smsNotificationService->sendSmsWithTwilio();
        } elseif ($this->getApiVersion() == 'BulkSms') {
            $this->smsNotificationService->sendSmsWithBulkSmsService();
        } else {
            $this->smsNotificationService->sendSmsViaOtherServices();
        }
    }

    private function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }

    private function setTrackingMesageText($message, $data)
    {
        $keywords   = [
            '{order_id}',
            '{trackingtitle}',
            '{tracknumber}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }
}
