var config = {
    map: {
        '*': {
          'Magento_Checkout/template/form/element/email.html': 
              'Sunbrains_SMSProfile/template/form/element/email.html',
          'Magento_OfflinePayments/template/payment/cashondelivery.html':
          	'Sunbrains_SMSProfile/template/payment/cashondelivery.html',
          'Magento_Checkout/template/authentication.html': 
              'Sunbrains_SMSProfile/template/authentication.html',
          'Magento_OfflinePayments/js/view/payment/method-renderer/cashondelivery-method' : 
              'Sunbrains_SMSProfile/js/payment/cashondelivery-method',    	
        }        
    }
};