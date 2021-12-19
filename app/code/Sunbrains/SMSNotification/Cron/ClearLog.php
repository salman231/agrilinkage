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
 
namespace Sunbrains\SMSNotification\Cron;

use Sunbrains\SMSNotification\Model\SMSLogFactory;
use Sunbrains\SMSNotification\Helper\Data as HelperData;

class ClearLog
{
    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @param SMSLogFactory $smslog
     * @param HelperData $dataHelper
     */
    public function __construct(
        HelperData $dataHelper,
        SMSLogFactory $smslog
    ) {
        $this->smslog = $smslog;
        $this->datahelper = $dataHelper;
    }

    /**
     * SmsLog clear for Cron request
     *
     * @return RedirectFactory
     */
    public function execute()
    {
        if ($this->datahelper->getSmsLogEnable() && $this->datahelper->getCronStatus()) {
            $sms  = $this->smslog->create();
            try {
                $sms->clearelog();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this;
    }
}
