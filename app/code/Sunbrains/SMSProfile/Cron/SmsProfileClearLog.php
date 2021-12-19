<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Cron;

use Sunbrains\SMSProfile\Model\SMSProfileLogFactory;
use Sunbrains\SMSProfile\Helper\Data as HelperData;

class SmsProfileClearLog
{

    /** @var SMSProfileLogFactory */
    private $smslog;

     /**  @var HelperData */
    private $datahelper;

    /**
     * @param SMSLogFactory $smslog
     * @param HelperData $dataHelper
     */
    public function __construct(
        HelperData $dataHelper,
        SMSProfileLogFactory $smslog
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
        if ($this->datahelper->getSmsProfileLogStatus() && $this->datahelper->getSmsProfileCronStatus()) {
            $sms  = $this->smslog->create();
            try {
                $sms->SmsProfileClearelog();
            } catch (\Exception $e) {
               $this->messageManager->addError($e->getMessage());
            }
        }
        return;
    }
}
