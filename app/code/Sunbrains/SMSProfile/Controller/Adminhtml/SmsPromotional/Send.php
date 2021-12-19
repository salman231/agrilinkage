<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Controller\Adminhtml\SmsPromotional;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sunbrains\SMSProfile\Model\SMSProfileService;
use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Reports\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;

class Send extends \Magento\Backend\App\Action
{
    /**  @var RedirectFactory */
    private $resultRedirect;

    /**  @var SMSProfileService */
    private $smsProfileService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var CustomerRepositoryInterface */
    private $customerRepository;

    /**  @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**  @var CollectionFactory */
    private $customerFactory;

    /**  @var QuoteCollectionFactory */
    private $quoteCollection;

    /**  @var StoreManagerInterface */
    private $store;

    /**  @var SubscriberCollectionFactory */
    private $subscriber;

    /**  @var Csv */
    private $csvProcessor;

     /**  @var Filesystem */
    private $filesystem;

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        CustomerRepositoryInterface $customerRepository,
        SMSProfileService $smsProfileService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $customerFactory,
        QuoteCollectionFactory $quoteCollection,
        StoreManagerInterface $store,
        SubscriberCollectionFactory $subscriber,
        Csv $csvProcessor,
        Filesystem $filesystem,
        HelperData $dataHelper
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->smsProfileService = $smsProfileService;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->datahelper = $dataHelper;
        $this->store = $store;
        $this->quoteCollection = $quoteCollection;
        $this->subscriber = $subscriber;
        $this->customerFactory = $customerFactory;
        $this->csvProcessor = $csvProcessor;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        $resultRedirect = $this->resultRedirect->create();
        if (!$this->datahelper->getModuleStatus()) {
                 $message  =__('Please Enable Module To Send SMS .');
                 $this->messageManager->addError($message);
                 return $resultRedirect->setPath('smsprofile/smspromotional/index');
        }

        if ($data) {
            $toNumber = array_unique($this->getToNumers($data));
            $message = $data['message'];
            if (empty($toNumber)) {
                 $message  =__('Contact Number is required to send text sms');
                 $this->messageManager->addError($message);
                 return $resultRedirect->setPath('smsprofile/smspromotional/index');
            }
            if($this->getApiVersion() == 'Twilio Api Service')
            {
                $this->smsProfileService->setToBinding($toNumber);
            } else {
                $this->smsProfileService->setToNumber($toNumber);
            }
            $this->smsProfileService->setMessageContent($message);
            $this->smsProfileService->setTransactionType($this->getTransactionType());
            $this->smsProfileService->setApiVersion($this->getApiVersion());
            $this->callSmsSending();
            return $resultRedirect->setPath('smsprofile/smsprofilelog/index');
        }
    }

    private function getApiVersion()
    {
        return  $this->datahelper->getSmsProfileApiGateWay();
    }

    /**  @return string */

    private function getTransactionType()
    {
        return 'Promotional Sms';
    }

    public function callSmsSending()
    {
        if($this->getApiVersion() == 'Twilio Api Service')
        {
            $this->smsProfileService->sendPromotionalSmsTextWithTwilio();
        } else {
            $this->smsProfileService->sendPromotionalSMSViaOtherServices();
            if ($this->datahelper->getApiReauestResponseXML() ) {
                $this->smsProfileService->sendPromotionalSMSViaOtherServicesXML();
            } else {
                $this->smsProfileService->sendPromotionalSMSViaOtherServices();
            }
        }
    }

    public function getToNumers($data)
    {
        $sendsms = $data['selectgrid'];
        $toNumber = array();

        switch($sendsms) {
            case 'customer':
               $customers = array_diff($data['selectedcustomer'] , ['on'] );
               $toNumber = $this->getCustomerNumber($customers);
               break;
            case 'customer_group':
                $customer_group = array_diff($data['selectedcustomergroup'] , ['on'] );
                $toNumber = $this->getCustomerNumberByGroup($customer_group);
                break;
            case 'custom':
               ($data['tonumbers'] != null) ? $toNumber = (explode(",",$data['tonumbers'])) : $toNumber =array() ;
                break;
            case 'abandoned_cart':
                $toNumber = $this->getCustomerNumberByAbandonedCart();
                break;
            case 'subscriber':
                $toNumber = $this->getCustomerNumberBySubscribed();
                break;
            case 'csv':
                $toNumber = $this->getCustomerNumberByImportedCsv($data);
                break;
        }
        return  $toNumber;
    }

    public function getCustomerNumber($customers)
    {
        $customerNumber = array();
        foreach ($customers as $customers) {
            $_customer = $this->customerRepository->getById($customers);
            $cattrValue = $_customer->getCustomAttribute('customer_mobile');
            if($cattrValue) {
                $customerNumber[] = $cattrValue->getValue();
            }
        }
        return $customerNumber;
    }

    public function getCustomerNumberByGroup($customer_group)
    {
        $collection = $this->customerFactory->create(); 
        $customerIds = array();
        $no = array();
        foreach ($customer_group as $customer_group) {
            $_customer = $collection->addFieldToFilter("group_id",$customer_group);
            foreach ($_customer as $_customer) {
                 $customerIds[] = $_customer->getId();
            }
        }
        $no = $this->getCustomerNumber($customerIds);
        return $no;
    }

    public function getCustomerNumberByAbandonedCart()
    {
        $collection  = $this->quoteCollection->create();
        $store_id = $this->store->getStore()->getId();
        $collection->prepareForAbandonedReport([$store_id]);
        $rows = $collection->load();
        $customerIds = array();
        $no = array();
        foreach ($rows as $key => $row) {
            $customerIds[] = $row->getCustomerId();
        }
        $no = $this->getCustomerNumber($customerIds);
        return $no;
    }

    public function getCustomerNumberBySubscribed()
    {
        $customerIds = array();
        $no = array();
        $subscriber =$this->subscriber->create();
        foreach ($subscriber as $subscriber) {
            $customerIds[] = $subscriber->getCustomerId();
        }
        $no = $this->getCustomerNumber($customerIds);
        return $no;
    }

    public function getCustomerNumberByImportedCsv($data)
    {
        $no =[];
        if (isset($data['csv']['0'])) {
          $csv_name = $data['csv'][0]['name'];

          $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                        ->getAbsolutePath(). 'smscsv/import/' . $csv_name;
          $importRawData = $this->csvProcessor->getData($mediaPath);
          foreach ($importRawData as $import) {
              if (is_numeric ($import[0])) {
                if(strpos($import[0], '+') === false && $this->getApiVersion() == 'Twilio Api Service' ) {
                    $no[] =  '+'.$import[0];

                } else if (strpos($import[0], '+') === false &&  $this->datahelper->getSmsProfileApiCountryRequired() && $this->getApiVersion() != 'Twilio Api Service') {
                       $no[] =  '+'.$import[0];
                } else {
                    $no[] = $import[0];
                }    
              }
              
          }         
        }
        return $no;
    }
}
