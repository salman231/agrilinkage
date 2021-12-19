<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Sunbrains\SMSProfile\Model\SMSProfileLogFactory;
use Sunbrains\SMSProfile\Helper\Data as HelperData;

class SmsProfileLog extends Template
{
    /** @var SMSNotificationService */
    private $smsService;

    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /**  @var HelperData */
    private $datahelper;

    /**
     *Construct
     *
     * @param Context $context
     * @param HelperData $dataHelper
     * @param SMSProfileLogFactory $smsprofilelog
     */
    public function __construct(
        Context $context,
        HelperData $dataHelper,
        SMSProfileLogFactory $smsprofilelog
    ) {
        $this->smsprofilelog = $smsprofilelog;
        $this->datahelper = $dataHelper;
        parent::__construct($context);
    }

    public function getSMSProfileDetailsById($id)
    {
        $sms = $this->smsprofilelog->create();
        $sms->load($id);
        return $sms;
    }
}
