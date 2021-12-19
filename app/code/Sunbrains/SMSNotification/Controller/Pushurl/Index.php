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
 
namespace Sunbrains\SMSNotification\Controller\Pushurl;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Sunbrains\SMSNotification\Model\SMSLogFactory;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Sunbrains\SMSNotification\Model\SMSNotificationService;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Index extends Action
{
    /**
     * @var SMSNotificationService
     */
    private $smsService;

    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @param Context $context
     * @param SMSLogFactory $smslog
     * @param SMSNotificationService $smsService
     * @param HelperData $dataHelper
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        HelperData $dataHelper,
        SMSNotificationService $smsService,
        JsonHelper $jsonHelper,
        SMSLogFactory $smslog
    ) {
        parent::__construct($context);
        $this->smslog = $smslog;
        $this->smsService = $smsService;
        $this->datahelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * SmsLog Update Staus by webhook.
     *
     * @return RedirectFactory
     */
    public function execute()
    {
        if ($this->datahelper->getApiGateway() == "Other") {
             $this->updateStatus();
        }
    }

    public function updateStatus()
    {
        $other_url =$this->datahelper->getApiEndPoints();
        if (strpos($other_url, 'msg91') !== false) {
            $this->updateStatusForMsg91();
        }
    }

    public function updateStatusForMsg91()
    {
        $request = $this->getRequest()->getParams();
    
        foreach ($request as $key => $value) {
             // request id
            $requestID = $value['requestId'];
            $userId = $value['userId'];
            $senderId = $value['senderId'];
            $smslog = $this->smslog->create();
            foreach ($value['report'] as $key1 => $value1) {
                //detail description of report
                $desc = $value1['desc'];

                // status of each number
                $status = $value1['status'];

                $_sms = $smslog->getCollection();
                $_sms->addFieldToFilter('s_id', $requestID);

                foreach ($_sms as $sms) {
                    $additionalData['toNumber'] = $sms->getRecipientPhone();
                    $additionalData["transactionType"] =$sms->getTransactionType();
                    $id ='';
                    if (is_string($sms->getMessageBody()) && $sms->getMessageBody() != null) {
                        preg_match_all('!\d+!', $sms->getMessageBody(), $matches);
                        if (isset($matches[0][0])) {
                            if (strlen($matches[0][0]) >= 9) {
                                $id = $matches[0][0];
                            } elseif (isset($matches[0][1])) {
                                $id = $matches[0][1];
                            }
                        }
                    }
                    $additionalData['orderNo'] = $id;
         
                    if ($desc =='Failed' || $desc =='Rejected') {
                        $sms->setIsError(1);
                        $error = __('Delivery Failed due to unknown reason.');
                        $sms->setErrorMessage($error);
                        $smslog->sendFailureMail($additionalData, $error);
                    }
                    $sms->setStatus($desc);
                    $this->saveSMS($sms);
                }
            }
        }
        return $this;
    }

    public function saveSMS($sms)
    {
        return $sms->save();
    }
}
