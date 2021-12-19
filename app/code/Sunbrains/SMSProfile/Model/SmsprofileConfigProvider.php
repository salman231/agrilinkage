<?php
namespace Sunbrains\SMSProfile\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Sunbrains\SMSProfile\Helper\Data as HelperData;

class SmsprofileConfigProvider implements ConfigProviderInterface
{
    /**
     * Constructor
     * @param SMSProfileLogFactory $smsprofilelog
     * @param HelperData $dataHelper
     * @param EncryptorInterface $encryptor
     * @param ClientFactory $twilioClientFactory
     */

    public function __construct(
        HelperData $dataHelper
    ) {
        $this->datahelper = $dataHelper;        
    }

    public function getConfig()
    {
        $config = [];
        $config['otplogin'] = 'login_pwd';
        $config['otpcod'] = 0;
        $config['otpnote'] = '';
        if ($this->datahelper->getModuleStatus()) {
            $config['otplogin'] = $this->datahelper->getSmsProfileOtpOnLogin();
            $config['otpcod'] = $this->datahelper->getOtpForCOD();
            if($this->datahelper->getPhoneNote()) {
                $config['otpnote'] = $this->datahelper->getPhoneNote();
            }
        }    
        return $config;
    }
}