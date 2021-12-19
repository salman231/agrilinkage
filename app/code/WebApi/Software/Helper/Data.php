<?php
namespace WebApi\Software\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const IS_ENABLED = 'webapisoftware/general/enable';
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
		$this->curlClient = $curl;
		$this->logger = $logger;
		$this->_storeManager = $storeManager;
    }
	
	public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }
	
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::IS_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getCurrentStoreId()
        );
    }
	
	public function getDataApi($serviceUrl)
	{
		try{
			$this->curlClient->get($serviceUrl);
			//response will contain the output in form of JSON string
			return $this->curlClient->getBody();
		}
		catch (\Exception $e) {
			$this->logger->critical('Error Curl', ['exception' => $e->getMessage()]);
		}
	}
	
	public function getAllCommodityRetailPrices()
	{
		$serviceUrl = $this->getServerUrl().'/api/Price/getAllCommodityRetailPrices';
		$response = json_decode($this->getDataApi($serviceUrl),true);
		return $response;
	}
	
	public function getServerUrl()
    {
        return 'http://webservices.ja-mis.com';
    }
}