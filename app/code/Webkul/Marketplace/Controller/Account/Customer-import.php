<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Webkul Marketplace Customer History Controller.
 */
class Customer extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $helperData;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    protected $csv;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;



    /**
     * @param Context                         $context
     * @param PageFactory                     $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Webkul\Marketplace\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Marketplace\Helper\Data $helperData,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\Marketplace\Model\SellerFactory $sellerFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->_resultPageFactory = $resultPageFactory;
        $this->csv = $csv;
        $this->_moduleReader = $moduleReader;
        $this->customerFactory  = $customerFactory;
        $this->_sellerFactory = $sellerFactory;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    /*public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(
            'Magento\Customer\Model\Url'
        )->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }*/

    /**
     * Seller's Customer history page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {


        $this->importCustomerData();

        exit;
        $helper = $this->helperData;
        if (!$helper->getSellerProfileDisplayFlag()) {
            $this->getRequest()->initForward();
            $this->getRequest()->setActionName('noroute');
            $this->getRequest()->setDispatched(false);
            return false;
        }
        $isPartner = $helper->isSeller();
        if ($isPartner == 1) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->_resultPageFactory->create();
            if ($helper->getIsSeparatePanel()) {
                $resultPage->addHandle('marketplace_layout2_account_customer');
            }
            $resultPage->getConfig()->getTitle()->set(
                __('Marketplace Customers')
            );
            return $resultPage;
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    public function importCustomerData()
    {
        //echo "sdasdas";exit;
        $directory = $this->_moduleReader->getModuleDir('etc', 'Webkul_Marketplace');
        $file = $directory . '/final-import-farmer.csv';
        if (file_exists($file)) {
            //echo "AAAAA";exit;
            $data = $this->csv->getData($file);
            // This skips the first line of your csv file, since it will probably be a heading. Set $i = 0 to not skip the first line.

            // Instantiate object (this is the most important part)

            for($i=1; $i<count($data); $i++) {
				//echo "AAAAA";exit;
                // echo "<pre>"; 
				//print_r($data);
				//exit;
                $customer   = $this->customerFactory->create();

                $farmerName = explode(" ", $data[$i][0]);
                //echo "<br>";
                $ABISnumber = $data[$i][1];
                //echo "<br>";
                $Mobilenumber = trim($data[$i][2]);
                if (strpos($Mobilenumber,"-") !== false) {
                    $Mobilenumber = str_replace('-', '', $Mobilenumber) ;
                }

                //echo "<br>";
                //$products = explode(" ", $data[$i][3]);
                //echo "<br>";
                $Paris = $data[$i][3];
                $email = $data[$i][4];

                $firstName = $farmerName[0]; // piece1
                $lastName =  $farmerName[1]; // piece2

                // Get Website ID
                $websiteId  = 1 ;//$this->storeManager->getWebsite()->getWebsiteId();
                $customer->setWebsiteId($websiteId);

                // Preparing data for new customer
                $customer->setEmail($email);
                $customer->setFirstname($firstName);
                $customer->setLastname($lastName);
                $customer->setPassword("Alfarmer@123");
                $customer->setCustomerMobile($Mobilenumber);
                // Save data

                try {
                    $customer->save();
                } catch (\Exception $e) {
                    echo $i.$firstName;
                    echo $e->getMessage(); exit;
                }

                $seller = $this->_sellerFactory->create();
                $seller->setSellerId($customer->getId());
                $seller->setAbisNumber($ABISnumber);
                $seller->setParish($Paris);
                $seller->setIsSeller('1');

                try {
                    $seller->save();
                } catch (\Exception $e) {
                    echo $i.$firstName;
                    echo $e->getMessage(); exit;
                }

                //Save Product
                /*$collection1 = $this->_objectManager->create(
                    'Webkul\Marketplace\Model\Product'
                );*/

                //$productIds = $products;

                /*foreach ($productIds as $productId){
                    $collection1->setMageproductId($productId);
                    $collection1->setSellerId($seller->getSellerId());
                    $collection1->setStatus(1);
                    $collection1->setAdminassign(1);
                    $collection1->setIsApproved(1);
                }

                try {
                    $collection1->save();
                } catch (\Exception $e) {
                    $e->getMessage();
                }*/
                //$this->saveFarmerData();
            }
        }
    }

    /*public function saveFarmerData($customerId)
    {

    }*/
}
