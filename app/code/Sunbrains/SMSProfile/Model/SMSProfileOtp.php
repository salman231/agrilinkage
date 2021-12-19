<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model;

use Magento\Framework\Model\AbstractModel;

class SMSProfileOtp extends AbstractModel
{
    const CACHE_TAG = 'smsprofileotp';

    protected $_cacheTag = 'smsprofileotp';
    
    protected $_eventPrefix = 'smsprofileotp';

    protected function _construct()
    {
        $this->_init('Sunbrains\SMSProfile\Model\ResourceModel\SMSProfileOtp');
    }
    
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
