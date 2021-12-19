<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SmsProfile\Model\Config\Source;

class SmsProfileComment implements \Magento\Config\Model\Config\CommentInterface
{
    public $_storeManager;
    
    public function __construct(
      \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager=$storeManager;
    }

    /**
     * Retrieve element comment by element value
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        //do some calculations here
        $storeurl = $this->_storeManager->getStore()
           ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);

       return __('If your service provider sends delivery report through the webhook for SMS which are processed and above 4 fields are not mandatory then specify url (eg.: "http://yourdomain.com/smsprofile/pushurl/index") in your service provider\'s account.');

    }
}
