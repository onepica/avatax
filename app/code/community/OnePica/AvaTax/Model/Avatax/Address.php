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

/**
 * The AvaTax Address Validator model.
 */
class OnePica_AvaTax_Model_Avatax_Address extends OnePica_AvaTax_Model_Abstract
{	
	/**
	 * An array of previously checked addresses
	 * Example: $_cache[$key] = serialize($resultObjectFromAvalara)
	 *
	 * @var array
	 */
	protected $_cache = array();
	
	/**
	 * The Mage Address object
	 *
	 * @var Mage_Customer_Model_Address_Abstract
	 */
	protected $_mageAddress = null;

	/**
	 * The AvaTax Request Address object.
	 * This is a Ava address copy of the Mage address attributes.
	 *
	 * @var Address
	 */
	protected $_requestAddress = null;

	/**
	 * The AvaTax Response (Normalized) Address object.
	 * This is the normalized Ava address returned by the validation request.
	 *
	 * @var ValidAddress
	 */
	protected $_responseAddress = null;

	/**
	 * Saves the store id
	 *
	 * @var int
	 */
	protected $_storeId = null;
	
	/**
	 * Loads any saved addresses in session
	 *
	 */
	public function __construct() {
		$addresses = Mage::getSingleton('avatax/session')->getAddresses();
		if(is_array($addresses)) {
			$this->_cache = $addresses;
		}
		parent::__construct();
	}
	
	/**
	 * Saves any current addresses to session
	 *
	 */
	public function __destruct() {
		Mage::getSingleton('avatax/session')->setAddresses($this->_cache);
		
		if(method_exists(get_parent_class(), '__destruct')) {
			parent::__destruct();
		}		
	}

	/**
	 * Sets the Mage address.
	 *
	 * @return OnePica_AvaTax_Model_Validate_Address
	 */
	public function setAddress (Mage_Customer_Model_Address_Abstract $address)
	{
		$this->_storeId = Mage::app()->getStore()->getId();
		$this->_mageAddress = $address;
		$this->_convertRequestAddress();
		return $this;
	}

	/**
	 * Sets attributes from the Mage address on the AvaTax Request address.
	 *
	 * @return OnePica_AvaTax_Model_Validate_Address
	 */
	protected function _convertRequestAddress ()
	{
		if (!$this->_requestAddress) {
			$this->_requestAddress = new Address();
		}
		$this->_requestAddress->setLine1($this->_mageAddress->getStreet(1));
		$this->_requestAddress->setLine2($this->_mageAddress->getStreet(2));
		$this->_requestAddress->setCity($this->_mageAddress->getCity());
		$this->_requestAddress->setRegion($this->_mageAddress->getRegionCode());
		$this->_requestAddress->setCountry($this->_mageAddress->getCountry());
		$this->_requestAddress->setPostalCode($this->_mageAddress->getPostcode());

		return $this;
	}

	/**
	 * Sets attributes from the AvaTax Response address on the Mage address.
	 *
	 * @return OnePica_AvaTax_Model_Validate_Address
	 */
	protected function _convertResponseAddress ()
	{
		$street = array($this->_responseAddress->getLine1(), $this->_responseAddress->getLine2());
		$region = Mage::getModel('directory/region')->loadByCode($this->_responseAddress->getRegion(), $this->_mageAddress->getCountryId());

		$this->_mageAddress->setStreet($street)
							->setCity($this->_responseAddress->getCity())
							->setRegionId($region->getId())
							->setPostcode($this->_responseAddress->getPostalCode())
							->setCountryId($this->_responseAddress->getCountry())
							->save()
							->setAddressNormalized(true);
		return $this;
	}

	/**
	 * Validates the address with the AvaTax validation API.
	 * Returns true on success and an array with an error on failure.
	 *
	 * @return true|array
	 */
	public function validate () {
		if (!$this->_mageAddress) {
			throw new OnePica_AvaTax_Model_Avatax_Address_Exception($this->__('An address must be set before validation.'));
		}
		
		$config = Mage::getSingleton('avatax/config')->init($this->_storeId);
		$isAddressValidationOn = Mage::helper('avatax')->isAddressValidationOn($this->_mageAddress, $this->_storeId);
		$isAddressNormalizationOn = Mage::helper('avatax')->isAddressNormalizationOn($this->_mageAddress, $this->_storeId);
		$isQuoteActionable = Mage::helper('avatax')->isObjectActionable($this->_mageAddress->getQuote(), $this->_mageAddress);
		
		//if there is no use cases for AvaTax services, return address as valid without doing a lookup
		if(!$isAddressValidationOn && !$isAddressNormalizationOn && !$isQuoteActionable) {
			return true;
		}

		//lookup in AvaTax (with caching)
		$key = $this->_mageAddress->getCacheHashKey();
		if (array_key_exists($key, $this->_cache)) {
			$result = unserialize($this->_cache[$key]);
		} else if ($this->_mageAddress->getPostcode() && $this->_mageAddress->getPostcode() != '-') { 
			$session = Mage::getSingleton('checkout/session');
			if ($session->getPostType() == 'onepage')
			{
				$requiredFields = explode(",", $this->getHelper()->getFieldRequiredList());
				$fieldRules = explode(",", $this->getHelper()->getFieldRule());
				foreach ($requiredFields as $field)
				{
					$requiredFlag = 0;
					foreach ($fieldRules as $rule)
					{   
						if (preg_match("/street\d/", $field)) $field = "street";
						if ($field == "country") $field = "country_id";
						if ($this->_mageAddress->getData($field) == $rule || !$this->_mageAddress->getData($field))
						{
							$requiredFlag = 1;
						}
					}
					if ($requiredFlag)
					{   
						$errors = array();
						$errors[] = $this->__('Invalid ').$this->__($field);
						return $errors;
					}
				}
			}
                                                  
			$client = $config->getAddressConnection();
			$request = new ValidateRequest($this->_requestAddress, TextCase::$Mixed, 0);
			$request->setTaxability(true);
			$result = $client->Validate($request);
			$this->_log($request, $result, $this->_storeId, $client);
			$this->_cache[$key] = serialize($result);
		} else {   
			$errors = array();
			$errors[] = $this->__('Invalid ZIP/Postal Code.');
			return $errors;
		}
		
		//normalization
		if ($isAddressNormalizationOn && $result->getResultCode() == SeverityLevel::$Success) {
			$responseAddress = $result->getValidAddresses();
			$responseAddress = array_pop($responseAddress);
			if ($responseAddress instanceof ValidAddress) {
				$this->_responseAddress = $responseAddress;
				$this->_convertResponseAddress();
			} else {
				throw new OnePica_AvaTax_Model_Avatax_Address_Exception($this->__('Invalid response address type.'));
			}				
		}
		
		//validation
		if($isAddressValidationOn == 1) {
			if($result->getResultCode() == SeverityLevel::$Success) {
				$this->_mageAddress->setAddressValidated(true);
				return true;
			} else {
				$errors = array();
				foreach ($result->getMessages() as $message) {
					$errors[] = $this->__($message->getSummary());
				}
				return $errors;
			}
			
		} else if ($isAddressValidationOn == 2) {
			$this->_mageAddress->setAddressValidated(true);
			if($result->getResultCode() == SeverityLevel::$Success) { 
				return true;
			} else {
				$this->_mageAddress->setAddressNotified(true);
				foreach ($result->getMessages() as $message) { 
					Mage::getSingleton('core/session')->addNotice($this->__($message->getSummary()));
				}
				return true;
			}

		//a valid address isn't required, but Avalara has to say there is 
		//enough info to drill down to a tax jurisdiction to calc on
		} elseif(!$isAddressValidationOn && $isQuoteActionable) {
			if($result->isTaxable()) {
				$this->_mageAddress->setAddressValidated(true);
				return true;
			} else {
				$errors = array();
				foreach ($result->getMessages() as $message) {
					$errors[] = $this->__($message->getSummary());
				}
				return $errors;
			}
		}
		
		return true;
	}
}
