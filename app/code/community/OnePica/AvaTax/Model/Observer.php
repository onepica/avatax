<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


class OnePica_AvaTax_Model_Observer extends Mage_Core_Model_Abstract
{

	/**
	 * Sets the collectTotals tax node based on the extensions enabled/disabled status
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer) {
		$storeId = $observer->getEvent()->getQuote()->getStoreId();
		if(Mage::getStoreConfig('tax/avatax/action', $storeId) != OnePica_AvaTax_Model_Config::ACTION_DISABLE) {
			Mage::getConfig()->setNode('global/sales/quote/totals/tax/class', 'avatax/sales_quote_address_total_tax');
		}
	}

	/**
	 * Create a sales invoice record in Avalara
	 *
	 * @param Varien_Event_Observer $observer
	 */
    public function salesOrderInvoiceSaveAfter(Varien_Event_Observer $observer) {
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
    	$invoice = $observer->getEvent()->getInvoice();

        $existingInvoiceInQueue = Mage::getModel('avatax_records/queue')->loadInvoiceByIncrementId($invoice->getIncrementId());
        if ($existingInvoiceInQueue->getId()) {
            return $this;
        }

        if(!$invoice->getOrigData($invoice->getIdFieldName()) && Mage::helper('avatax')->isObjectActionable($invoice)) {
		    Mage::getModel('avatax_records/queue')
		    	->setEntity($invoice)
		    	->setType(OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE)
		    	->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING)
		    	->save();
    	}
    }
    
	/**
	 * Create a return invoice record in Avalara
	 *
	 * @param Varien_Event_Observer $observer
	 */
    public function salesOrderCreditmemoSaveAfter(Varien_Event_Observer $observer) {
    	$creditmemo = $observer->getEvent()->getCreditmemo();
    	if(!$creditmemo->getOrigData($creditmemo->getIdFieldName()) && Mage::helper('avatax')->isObjectActionable($creditmemo)) {
		    Mage::getModel('avatax_records/queue')
		    	->setEntity($creditmemo)
		    	->setType(OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_CREDITMEMEO)
		    	->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_PENDING)
		    	->save();
    	}
    }
    
    public function multishippingSetShippingItems(Varien_Event_Observer $observer) {
    	/* @var $quote Mage_Sales_Model_Quote */
    	$quote = $observer->getEvent()->getQuote();
    	
    	$errors = array();
    	$normalized = false;
    	$store = Mage::getModel('core/store')->load($quote->getStoreId());
    	$addresses  = $quote->getAllShippingAddresses();
    	$message = Mage::getStoreConfig('tax/avatax/validate_address_message', $store);
    	foreach ($addresses as $address) {
    		/* @var $address OnePica_AvaTax_Model_Sales_Quote_Address */
    		if ($address->validate() !== true) {
    			$errors[] = sprintf($message, $address->format('oneline'));
    		}
    		if ($address->getAddressNormalized()) {
    			$normalized = true;
    		}
    	}
    	
    	$session = Mage::getSingleton('checkout/session');
    	if ($normalized) {
    		$session->addNotice(Mage::getStoreConfig('tax/avatax/multiaddress_normalize_message', $store));
    	}
    	
    	if (!empty($errors)) {
    		Mage::throwException(implode('<br />', $errors));
    	}
    }
    
	/**
	 * Observer push data to Avalara
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return null
	 */
    public function processQueue($schedule) {
    	Mage::getModel('avatax_records/queue_process')->run();
    }
    
	/**
	 * Test for required values when admin config setting related to the this extension are changed
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return bool
	 */
    public function adminSystemConfigChangedSectionTax($schedule) {
    	Mage::app()->cleanCache('block_html');
    	
    	$session = Mage::getSingleton('adminhtml/session');
    	$storeId = Mage::getModel('core/store')->load($schedule->getEvent()->getStore())->getStoreId();
    	$warnings = array();
    	$errors = array();
    	
		if(strpos(Mage::getStoreConfig('tax/avatax/url', $storeId), 'development.avalara.net') !== false) {
			$warnings[] = Mage::helper('avatax')->__('You are using the AvaTax development connection URL. If you are receiving errors about authentication, please ensure that you have a development account.');
		}
    	if(Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_DISABLE) {
    		$warnings[] = Mage::helper('avatax')->__('All AvaTax services are disabled');
    	}
    	if(Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_CALC) {
    		$warnings[] = Mage::helper('avatax')->__('Orders will not be sent to the AvaTax system');
    	}
    	if(Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_CALC_SUBMIT) {
    		$warnings[] = Mage::helper('avatax')->__('Orders will be sent but never committed to the AvaTax system');
    	}
    	
    	$ping = Mage::getSingleton('avatax/avatax_ping')->ping($storeId);
    	if($ping !== true) {
    		$errors[] = $ping;
    	}
    	if(!Mage::getStoreConfig('tax/avatax/url', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter a connection URL');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/account', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter an account number');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/license', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter a license key');
    	}
    	if(!is_numeric(Mage::getStoreConfig('tax/avatax/log_lifetime'))) {
    		$errors[] = Mage::helper('avatax')->__('You must enter the number of days to keep log entries');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/company_code', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter a company code');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/shipping_sku', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter a shipping sku');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/adjustment_positive_sku', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter an adjustment refund sku');
    	}
    	if(!Mage::getStoreConfig('tax/avatax/adjustment_negative_sku', $storeId)) {
    		$errors[] = Mage::helper('avatax')->__('You must enter an adjustment fee sku');
    	}
    	
    	if(!class_exists('SoapClient')) {
    		$errors[] = Mage::helper('avatax')->__('The PHP class SoapClient is missing. It must be enabled to use this extension. See %s for details.', '<a href="http://www.php.net/manual/en/book.soap.php" target="_blank">http://www.php.net/manual/en/book.soap.php</a>');
    	}
    	
    	if(!function_exists('openssl_sign') && count($errors)) {
    		$key = array_search(Mage::helper('avatax')->__('SSL support is not available in this build'), $errors);
    		if(isset($errors[$key])) unset($errors[$key]);
    		$errors[] = Mage::helper('avatax')->__('SSL must be enabled in PHP to use this extension. Typically, OpenSSL is used but it is not enabled on your server. This may not be a problem if you have some other form of SSL in place. For more information about OpenSSL, see %s.', '<a href="http://www.php.net/manual/en/book.openssl.php" target="_blank">http://www.php.net/manual/en/book.openssl.php</a>');
    	}

        if(!Mage::getResourceModel('cron/schedule_collection')->count()) {
            $warnings[] = Mage::helper('avatax')->__('It appears that Magento\'s cron scheduler is not running. For more information, see %s.', '<a href="http://www.magentocommerce.com/wiki/how_to_setup_a_cron_job" target="_black">How to Set Up a Cron Job</a>');
        }
    	
    	
    	if(count($errors) == 1) {
			$session->addError(implode('', $errors));
    	} elseif(count($errors)) {
			$session->addError(Mage::helper('avatax')->__('Please fix the following issues:') . '<br /> - ' . implode('<br /> - ', $errors));
    	}
    	
    	if(count($warnings) == 1) {
			$session->addWarning(implode('', $warnings));
    	} elseif(count($warnings)) {
			$session->addWarning(Mage::helper('avatax')->__('Please be aware of the following warnings:') . '<br /> - ' . implode('<br /> - ', $warnings));
    	}
    }
    
	/**
	 * Observer to clean the log every so often so it does not get too big.
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return (none)
	 */
    public function cleanLog($schedule) { 
		$days = floatval(Mage::getStoreConfig('tax/avatax/log_lifetime'));
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$connection->delete(
			Mage::getResourceModel('avatax_records/log')->getTable('avatax_records/log'),
			$connection->quoteInto('created_at < DATE_SUB(UTC_DATE(), INTERVAL ? DAY)', $days)
		);
    }
    
}