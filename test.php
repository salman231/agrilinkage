<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

try
{
    $OM = \Magento\Framework\App\ObjectManager::getInstance();
    $collectionobj = $OM->create('\Magento\Customer\Model\Customer');
    $customercollection = $collectionobj->getCollection()
            ->addAttributeToSelect("*")->load();
    foreach($customercollection as $customer)
		echo "<pre>";print_R(json_encode($customer->getEmail()));die;
    {
       //$customer->delete();
    }

}
catch(\Exception $e)
{
    echo $e->getMessage();
    exit;
}