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
 
namespace Sunbrains\SMSNotification\Model;

use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\OrderFactory;

class SMSLog extends \Magento\Framework\Model\AbstractModel
{
    
    const CACHE_TAG = 'smslog';

    protected $_cacheTag = 'smslog';
    
    protected $_eventPrefix = 'smslog';

    /** @var HelperData */
    private $dataHelper;

    /**
     * Constructor
     * @param HelperData $dataHelper
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param $data
     */

    public function __construct(
        HelperData $dataHelper,
        Context $context,
        Registry $registry,
        OrderFactory $order,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->datahelper = $dataHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->order = $order;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    protected function _construct()
    {
        $this->_init('Sunbrains\SMSNotification\Model\ResourceModel\SMSLog');
    }
    
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @param string $sid
     * @return $this
     */
    public function setSid($sid)
    {
        return $this->setData('s_id', $sid);
    }

    /**
     * @return string
     */
    public function getSid()
    {
        return $this->getData('s_id');
    }

    /**
     * @param string $apiService
     * @return $this
     */
    public function setApiService($apiService)
    {
        return $this->setData('api_service', $apiService);
    }

    /**
     * @return string
     */
    public function getApiService()
    {
        return $this->getData('api_service');
    }

    /**
     * @param string $recipientPhone
     * @return $this
     */
    public function setRecipientPhone($recipientPhone)
    {
        return $this->setData('recipient_phone', $recipientPhone);
    }

    /**
     * @return string
     */
    public function getRecipientPhone()
    {
        return $this->getData('recipient_phone');
    }

    /**
     * @param string $transaction_type
     * @return $this
     */
    public function setTransactionType($transactionType)
    {
        return $this->setData('transaction_type', $transactionType);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->getData('transaction_type');
    }

    /**
     * @param string $messageBody
     * @return $this
     */
    public function setMessageBody($messageBody)
    {
        return $this->setData('message_body', $messageBody);
    }

    /**
     * @return string
     */
    public function getMessageBody()
    {
        return $this->getData('message_body');
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

     /**
      * @param int $isError
      * @return $this
      */
    public function setIsError($isError)
    {
        return $this->setData('is_error', $isError);
    }

    /**
     * @return int
     */
    public function getIsError()
    {
        return $this->getData('is_error');
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData('error_message', $errorMessage);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getData('error_message');
    }

    public function addLog($result, $additionalData, $error)
    {
        if ($this->datahelper->getSmsLogEnable()) {
            $this->setSid($this->getResultSid($result));
            $this->setApiService($additionalData['apiVersion']);
            $this->setRecipientPhone($additionalData['toNumber']);
            $this->setTransactionType($additionalData['transactionType']);
            $this->setMessageBody($this->getResultBody($result));
            $this->setStatus($this->getResultStatus($result));
            $this->setIsError(0);
            if (is_array($result)) {
                if (isset($result['ErrorMessage'])) {
                    if ($result['ErrorMessage'] != 'Success') {
                        $error = $result['ErrorMessage'];
                    }
                }
            }    
            if ($error != null) {
                $this->setIsError(1);
            }
            $this->setErrorMessage($this->getResultError($error));
            $this->save();
        }
        if ($error != null) {
            $this->sendFailureMail($additionalData, $error);
        }
    }

    public function getResultSid($result)
    {
        if ($result === 'fail') {
            return '';
        }
        if (is_array($result)) {
            /*Bulk SMS ID  & other's id*/
            if (isset($result[0]['id'])) {
                return  $result[0]['id'];
            } elseif (isset($result['sid'])) {
                return $result['sid'];
            } elseif (isset($result['MessageData'][0]['MessageParts'][0]['MsgId'])) {
                /* case of sms india hub*/
                return $result['MessageData'][0]['MessageParts'][0]['MsgId'];
            } elseif (isset($result['id'])) { /** case of messagebird*/
                return $result['id'];
            } else {
                return  '';
            }
        }
        /*Twioli SMS ID */
        return  $result->sid;
    }

    public function getResultBody($result)
    {
        if ($result === 'fail') {
            return '';
        }
        if (is_array($result)) {
            /*Bulk SMS & other body */
            if (isset($result[0]['body'])) {
                return $result[0]['body'];
            } elseif (isset($result['body'])) {
                return $result['body'];
            } elseif (isset($result['MessageData'][0]['MessageParts'][0]['Text'])) {
                /* case of sms india hub*/
                return $result['MessageData'][0]['MessageParts'][0]['Text'];
            } else {
                return '';
            }
        }
        /*Twiolio SMS body */
        return  $result->body;
    }

    public function getResultStatus($result)
    {
        if ($result === 'fail') {
            return 'fail';
        }
        if (is_array($result)) {
            /*Bulk SMS & other status */
            if (isset($result[0]['type'])) { /* case of bulksms*/
                return $result[0]['type'];
            } elseif (isset($result['status'])) {  /* case of msg91*/
                return $result['status'];
            } elseif (isset($result['ErrorMessage'])) {  /* case of sms india hub*/
                if ($result['ErrorMessage'] != 'Success') {
                    return 'failed';
                }
                return $result['ErrorMessage'];
            } elseif (isset($result['recipients']['items'][0]['status'])) {
                /* case of messagebird*/
                return $result['recipients']['items'][0]['status'];
            } else {
                return '';
            }
        }
        /*Twiolio SMS status */
        return  $result->status;
    }

    public function getResultError($error)
    {
        if ($error === null) {
            return '';
        }
        return $error;
    }

    public function sendFailureMail($additionalData, $error)
    {
        if ($this->datahelper->getNotifyFailureStatus()) {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder->setTemplateIdentifier('notification_template')
                  ->setTemplateOptions($this->getTemplateOption())
                  ->setTemplateVars($this->getTemplateVariable($additionalData, $error))
                  ->setFrom($this->getFromMail($this->datahelper->getNotifySenderMail()))
                  ->addTo($this->datahelper->getNotifyToMail())
                  ->getTransport();
            $transport->sendMessage();
        }
    }

    public function getTemplateVariable($additionalData, $error)
    {
        $messageText = __('Sms failure for the number \'<b>'.$additionalData["toNumber"].'</b>\'. and event is <b>'.$additionalData["transactionType"].'</b><br/> The error message is <font size="2" color="red">"'.$error.'"</font>');

        if ($additionalData["orderNo"] != '') {
            $order = $this->order->create()->loadByIncrementId($additionalData["orderNo"]);
            $add ='';
            if ($additionalData["transactionType"] == 'customer_invoice') {
                $invoice = $order->getInvoiceCollection()->getLastItem();
                $add = __('Invoice Id is '). $invoice->getIncrementId();
            } elseif ($additionalData["transactionType"] == 'customer_creditmemo') {
                $creditmemo = $order->getCreditmemosCollection()->getLastItem();
                $add = __('Creditmemo Id is '). $creditmemo->getIncrementId();
            } elseif ($additionalData["transactionType"] == 'customer_shipment' ||
                     $additionalData["transactionType"] == 'customer_shipment_tracking'
                ) {
                $shipment = $order->getShipmentsCollection()->getLastItem();
                $shipmentId = $shipment->getIncrementId();
                $add = __('shipment Id is ').$shipmentId;
            }
            $messageText = __('Sms failure for the number \'<b>'.$additionalData["toNumber"].'</b>\'. and event is <b>'.$additionalData["transactionType"].'</b> and order id is '.$additionalData["orderNo"] .' '.$add.'<br/> The error message is <font size="2" color="red">"'.$error.'"</font>');
        }

        $templateVars = array(
            'store' => $this->storeManager->getStore(),
            'message'   => $messageText,
        );

        return $templateVars;
    }

    public function getTemplateOption()
    {
        $templateOptions = array(
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        );

        return $templateOptions;
    }

    public function getFromMail($_fromMail)
    {
        $senderName = $this->_scopeConfig->getValue(
            'trans_email/ident_'.$_fromMail.'/name',
            ScopeInterface::SCOPE_STORE
        );
        $senderEmail = $this->_scopeConfig->getValue(
            'trans_email/ident_'.$_fromMail.'/email',
            ScopeInterface::SCOPE_STORE
        );

        $from = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];

        return $from;
    }

    public function clearelog()
    {
        $connection = $this->getCollection()->getConnection();
        $tableName = $this->getCollection()->getMainTable();
        $connection->truncateTable($tableName);
    }
}
