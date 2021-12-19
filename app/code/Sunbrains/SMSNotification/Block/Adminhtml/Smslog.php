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
 
namespace Sunbrains\SMSNotification\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Sunbrains\SMSNotification\Model\SMSLogFactory;
use Sunbrains\SMSNotification\Helper\Data as HelperData;
use Sunbrains\SMSNotification\Model\SMSNotificationService;

class Smslog extends \Magento\Backend\Block\Template
{
	/** @var SMSNotificationService */
    private $smsService;

    /** @var SMSLogFactory */
    private $smslog;

     /**  @var HelperData */
    private $datahelper;

    public function __construct(
    	\Magento\Backend\Block\Template\Context $context,
        HelperData $dataHelper,
        SMSNotificationService $smsService, 
        SMSLogFactory $smslog
    ) {
        $this->smslog = $smslog;
        $this->smsService = $smsService;
        $this->datahelper = $dataHelper;
        parent::__construct($context);
    }

    public function getSMSDetailsById($id)
    {
    	$sms = $this->smslog->create();
    	$sms->load($id);
    	return $sms;
    }

}