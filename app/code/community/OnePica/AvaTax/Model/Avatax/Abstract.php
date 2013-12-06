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


abstract class OnePica_AvaTax_Model_Avatax_Abstract extends OnePica_AvaTax_Model_Abstract
{
	
	/**
	 * Flag that states if there was an error
	 *
	 * @var bool
	 */
	protected static $_hasError = false;
	
	/**
	 * The request data object
	 *
	 * @var mixed
	 */
	protected $_request = null;
	
	/**
	 * Sets the company code on the request
	 *
	 * @return null
	 */
	protected function _setCompanyCode($storeId=null) {
		$config = Mage::getSingleton('avatax/config');
		$this->_request->setCompanyCode($config->getCompanyCode($storeId));
	}
	
	/**
	 * Sends a request to the Avatax server
	 *
	 * @param int $storeId
	 * @return mixed
	 */
	protected function _send($storeId) {
		$config = Mage::getSingleton('avatax/config')->init($storeId);
		$connection = $config->getTaxConnection();
		$result = null;
		$message = null;
		
		try { $result = $connection->getTax($this->_request); }
		catch(Exception $exception) { $message = $exception->getMessage(); }
		
		if(!isset($result) || !is_object($result) || !$result->getResultCode()) {
			$result = Mage::getModel('Varien_Object')
				->setResultCode(SeverityLevel::$Exception)
				->setActualResult($result)
				->setMessage($message);
		}
		
		$this->_log($this->_request, $result, $storeId, $connection);
		
		if($result->getResultCode() != SeverityLevel::$Success) {
			self::$_hasError = true;
			if(Mage::helper('avatax')->fullStopOnError($storeId)) {
				Mage::helper('avatax')->addErrorMessage($storeId);
			}
		}
		
		return $result;
	}
	
	/**
	 * Adds additional transaction based data
	 *
	 * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $object
	 */
	protected function _addGeneralInfo($object) {
		$storeId = $object->getStoreId();
		$this->_setCompanyCode($storeId);
        $this->_request->setDetailLevel(DetailLevel::$Document);
        $this->_request->setDocDate(date('Y-m-d'));
        $this->_request->setExemptionNo('');
    	$this->_request->setDiscount(0.00); //cannot be used in Magento
    	$this->_request->setSalespersonCode(Mage::helper('avatax')->getSalesPersonCode($storeId));
    	$this->_request->setLocationCode(Mage::helper('avatax')->getLocationCode($storeId));
		$this->_request->setCountry(Mage::getStoreConfig('shipping/origin/country_id', $storeId));
		$this->_request->setCurrencyCode(Mage::app()->getStore()->getBaseCurrencyCode());
		$this->_addCustomer($object);
		if($object instanceof Mage_Sales_Model_Order && $object->getIncrementId()) {
			$this->_request->setReferenceCode('Magento Order #' . $object->getIncrementId());
		}
	}
	
	/**
	 * Sets the customer info if available
	 *
	 * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $object
	 */
	protected function _addCustomer($object) {
		$format = Mage::getStoreConfig('tax/avatax/cust_code_format', $object->getStoreId());
		$customer = Mage::getModel('customer/customer');
		$customerCode = '';
		
		if($object->getCustomerId()) {
			$customer->load($object->getCustomerId());
			$taxClass = Mage::getModel('tax/class')->load($customer->getTaxClassId())->getOpAvataxCode();
        	$this->_request->setCustomerUsageType($taxClass);
		}
		
		switch($format) {
			case OnePica_AvaTax_Model_Source_Customercodeformat::LEGACY:
				if($customer->getId()) {
					$customerCode = $customer->getName() . ' (' . $customer->getId() . ')';
				} else {
                    $address = $object->getBillingAddress() ? $object->getBillingAddress() : $object;
					$customerCode = $address->getFirstname() . ' ' . $address->getLastname() . ' (Guest)';
				}
				break;
				
			case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_EMAIL:
				$customerCode = $object->getCustomerEmail() ? $object->getCustomerEmail() : $customer->getEmail();
				break;
				
			case OnePica_AvaTax_Model_Source_Customercodeformat::CUST_ID:
			default:
				$customerCode = $object->getCustomerId() ? $object->getCustomerId() : 'guest-'.$object->getId();
				break;
		}
		
		$this->_request->setCustomerCode($customerCode);
	}
	
	/**
	 * Adds the orgin address to the request
	 *
	 * @return Address
	 */
	protected function _setOriginAddress($store=null) {
		$country = Mage::getStoreConfig('shipping/origin/country_id', $store);
		$zip = Mage::getStoreConfig('shipping/origin/postcode', $store);
		$regionId = Mage::getStoreConfig('shipping/origin/region_id', $store);
		$state = Mage::getModel('directory/region')->load($regionId)->getCode();
		$city = Mage::getStoreConfig('shipping/origin/city', $store);
		$street = Mage::getStoreConfig('shipping/origin/street', $store);
		$address = $this->_newAddress($street, '', $city, $state, $zip, $country);
		return $this->_request->setOriginAddress($address);
	}
	
	/**
	 * Adds the shipping address to the request
	 *
	 * @param Address
	 * @return bool
	 */
	protected function _setDestinationAddress($address) {
		$street1 = $address->getStreet(1);
		$street2 = $address->getStreet(2);
		$city = $address->getCity();
		$zip = preg_replace('/[^0-9\-]*/', '', $address->getPostcode());
		$state = Mage::getModel('directory/region')->load($address->getRegionId())->getCode(); 
		$country = $address->getCountry();
		 
		if(($city && $state) || $zip) {
			$address = $this->_newAddress($street1, $street2, $city, $state, $zip, $country);
			return $this->_request->setDestinationAddress($address);
		} else {
			return false;
		}
	}
	
	/**
	 * Generic address maker
	 *
	 * @param string $line1 
	 * @param string $line2 
	 * @param string $city 
	 * @param string $state 
	 * @param string $zip 
	 * @param string $country 
	 * @return Address
	 */
	protected function _newAddress($line1, $line2, $city, $state, $zip, $country='USA') {
		$address = new Address();
		$address->setLine1($line1);
		$address->setLine2($line2);
		$address->setCity($city);
		$address->setRegion($state);
		$address->setPostalCode($zip);
		$address->setCountry($country);
		return $address;
	}
	
	/**
	 * Test to see if the product carries its own numbers or is calculated based on parent or children
	 *
	 * @param Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item|mixed $item 
	 * @return bool
	 */
	public function isProductCalculated($item) {
		try {
			if($item->isChildrenCalculated() && !$item->getParentItem()) {
				return true;
			}
			if(!$item->isChildrenCalculated() && $item->getParentItem()) {
				return true;
			}
		} catch(Exception $e) { }
		return false;
	}
	
	/**
	 * Adds a comment to order history. Method choosen based on Magento version.
	 *
	 * @param Mage_Sales_Model_Order
	 * @param string
	 * @return self
	 */
	protected function _addStatusHistoryComment($order, $comment) {
		if(method_exists($order, 'addStatusHistoryComment')) {
			$order->addStatusHistoryComment($comment)->save();;
		} elseif(method_exists($order, 'addStatusToHistory')) {
			$order->addStatusToHistory($order->getStatus(), $comment, false)->save();;
		}
		return $this;
	}
}
