<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Console;

use Sunbrains\SMSProfile\Helper\Data as HelperData;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

class ProfileSavePhone extends Command
{
    /**
     * @var CustomerFactory     
     */
    private $customer;

    /**
     * @var CustomerFactory     
     */
    private $address;

    /**
     * @var CustomerFactory     
     */
    private $customerReource;

    /** @var \Magento\Framework\App\State **/
    private $state;

    /**
     * Constuctor
     * @param CustomerFactory $customer
     * @param AddressFactory $address
     * @param CustomerResourceModel $customerReource
     * @param HelperData $dataHelper    
     */
    
    public function __construct(
        CustomerFactory $customer,
        AddressFactory $address,
        CustomerResourceModel $customerReource,
        State $state,
        HelperData $dataHelper
    ) {
        $this->datahelper = $dataHelper;
        $this->state = $state;
        $this->customer = $customer;
        $this->address = $address;
        $this->customerReource = $customerReource;
        parent::__construct();
    }
 
   protected function configure()
   {
       $this->setName('smsprofile:savephone');
       $this->setDescription('Save phone in customer eav attribute');
       
       parent::configure();
   }
   protected function execute(InputInterface $input, OutputInterface $output)
   {
       $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); 
       $add_type =  $this->datahelper->getAddressType();
       $customer = $this->customer->create()->getCollection();
       $customer->addAttributeToSelect("*")->load();
       foreach ($customer as $customer) {
         $telephone = $this->getCustomerTelephone($customer,$add_type);
         if ($telephone != null) {
            $this->SaveCutomerMobile($customer, $telephone);
         }
       }

       $output->writeln("Save customer's phone number successfully.");
   }

    public function getCustomerTelephone($customer,$add_type)
    {
        $tel = '';
        switch ($add_type) {
            case 'shipping_add':
                $shippingAddressId = $customer->getDefaultShipping();
                $address = $this->address->create()->load($shippingAddressId);
                $tel = $address->getTelephone();
                break;
            case 'billing_add':
                $billingAddressId = $customer->getDefaultBilling();
                $address = $this->address->create()->load($billingAddressId);
                $tel = $address->getTelephone();
                break;
            case 'first_add':
                $address = $customer->getAddresses();
                $_tel =[];
                foreach ($address as $address) {
                    $_tel[] = $address->getTelephone();
                }
                if(!(is_null($_tel))){
                    $tel = (isset($_tel[0])) ? $_tel[0] :'' ;
                }
                break;
            case 'last_add':
                $address = $customer->getAddresses();
                foreach ($address as $address) {
                    $tel = $address->getTelephone();
                }
                break;
        }
        if (is_numeric($tel)) {
            return $tel;
        }
        return null;
    }

   public function SaveCutomerMobile($customer,$telephone)
    {
        if ($customer->getCustomerMobile() == null) {
            $_customer =  $this->customer->create()->load($customer->getId());
            $customerData = $_customer->getDataModel();
            $customerData->setCustomAttribute('customer_mobile',$telephone);
            $_customer->updateData($customerData);

            $customerResource = $this->customerReource->create();
            $customerResource->saveAttribute($_customer, 'customer_mobile');
        }
    }
}
