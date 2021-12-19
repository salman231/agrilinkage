<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote\Payment;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Vault\Model\Method\Vault;

class CodValidationAssigner extends AbstractDataAssignObserver
{
    /**
     * @var PaymentTokenManagementInterface
     */
    private $paymentTokenManagement;
    /**
     * PaymentTokenAssigner constructor.
     * @param PaymentTokenManagementInterface $paymentTokenManagement
     */
    public function __construct(
        PaymentTokenManagementInterface $paymentTokenManagement
    ) {
        $this->paymentTokenManagement = $paymentTokenManagement;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }
        
        $_paymentModel = $this->readPaymentModelArgument($observer);
        
        if (!$_paymentModel instanceof Payment) {
            return;
        }
        if(isset($additionalData['codotp']) && $data->getMethod() == 'cashondelivery') {
            $_paymentModel->setAdditionalInformation(
                [
                    'codotp' => $additionalData['codotp']
                ]
            );
        }
        return $_paymentModel;
    }
}
