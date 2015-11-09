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
 * Class OnePica_AvaTax_Model_Service_Avatax_Address
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax_Address extends OnePica_AvaTax_Model_Service_Avatax_Abstract
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
     */
    public function __construct($data)
    {
        $addresses = Mage::getSingleton('avatax/session')->getAddresses();
        if (is_array($addresses)) {
            $this->_cache = $addresses;
        }
        if (isset($data['service_config'])) {
            $this->setServiceConfig($data['service_config']);
        }
        if (isset($data['address'])) {
            $this->setAddress($data['address']);
        }
        parent::_construct();
    }

    /**
     * Saves any current addresses to session
     */
    public function __destruct()
    {
        Mage::getSingleton('avatax/session')->setAddresses($this->_cache);

        if (method_exists(get_parent_class(), '__destruct')) {
            parent::__destruct();
        }
    }

    /**
     * Sets the Mage address.
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return $this
     */
    public function setAddress(Mage_Customer_Model_Address_Abstract $address)
    {
        $this->_storeId = Mage::app()->getStore()->getId();
        $this->_mageAddress = $address;
        $this->_convertRequestAddress();
        return $this;
    }

    /**
     * @return Mage_Customer_Model_Address_Abstract
     */
    public function getMageAddress()
    {
        return $this->_mageAddress;
    }

    /**
     * Get request address object
     * @return Address
     */
    public function getLocationAddressObject()
    {
        if (is_null($this->_requestAddress)) {
            $this->_requestAddress = new Address();
        }
        return $this->_requestAddress;
    }


    /**
     * Sets attributes from the Mage address on the AvaTax Request address.
     *
     * @return $this
     */
    protected function _convertRequestAddress()
    {
        $this->getLocationAddressObject()->setLine1($this->getMageAddress()->getStreet(1));
        $this->getLocationAddressObject()->setLine2($this->getMageAddress()->getStreet(2));
        $this->getLocationAddressObject()->setCity($this->getMageAddress()->getCity());
        $this->getLocationAddressObject()->setRegion($this->getMageAddress()->getRegionCode());
        $this->getLocationAddressObject()->setCountry($this->getMageAddress()->getCountry());
        $this->getLocationAddressObject()->setPostalCode($this->getMageAddress()->getPostcode());

        return $this;
    }

    /**
     * Validates the address with the AvaTax validation API.
     * Returns true on success and an array with an error on failure.
     *
     * @return Varien_Object
     * @throws OnePica_AvaTax_Model_Service_Exception_Address
     */
    public function validate()
    {
        if (!$this->getMageAddress()) {
            throw new OnePica_AvaTax_Model_Service_Exception_Address(
                $this->__('An address must be set before validation.')
            );
        }

        //lookup in AvaTax (with caching)
        $key = $this->getMageAddress()->getCacheHashKey();

        if (array_key_exists($key, $this->_cache)) {
            $result = unserialize($this->_cache[$key]);
        } else {
            $result = $this->_sendAddressValidationRequest();
            $this->_cache[$key] = serialize($result);
        }
        $response = new Varien_Object();
        if ($result instanceof ValidateResult) {
            $hasError = $result->getResultCode() === SeverityLevel::$Error;
            $response->setHasError($hasError);
            if ($hasError) {
                $errors = $result->getMessages();
                if ($response->getHasError() && is_array($errors) && isset($errors[0])) {
                    $convertErrors = array();
                    foreach ($errors as $element) {
                        $convertErrors[] = $element->getSummary();
                    }
                    $response->setErrors($convertErrors);
                }
            }
            $address = $result->getValidAddresses();
            if (is_array($address) && isset($address[0]) && $address[0]) {
                /** @var ValidAddress $address */
                $address = $address[0];
                $addressConvert = new Varien_Object();
                $addressConvert->setLine1($address->getLine1());
                $addressConvert->setLine2($address->getLine2());
                $addressConvert->setCity($address->getCity());
                $addressConvert->setRegion($address->getRegion());
                $addressConvert->setPostalCode($address->getPostalCode());
                $addressConvert->setCountry($address->getCountry());
                $addressConvert->setCounty($address->getCounty());
                $addressConvert->setFipsCode($address->getFipsCode());
                $addressConvert->setCarrierRoute($address->getCarrierRoute());
                $addressConvert->setPostNet($address->getPostNet());
                $addressConvert->setAddressType($address->getAddressType());
                $addressConvert->setLatitude($address->getLatitude());
                $addressConvert->setLongitude($address->getLongitude());
                $addressConvert->setTaxRegionId($address->getTaxRegionId());
                $response->setAddress($addressConvert);
            }
            /** Set is success */
            $response->setResolution($result->getResultCode() == SeverityLevel::$Success);
        }


        return $response;
    }

    /**
     * Validate address
     *
     * @return ValidateResult
     */
    protected function _sendAddressValidationRequest()
    {
        /** @var AddressServiceSoap $client */
        $client = $this->getServiceConfig()->getAddressConnection();
        $request = $this->_getAddressValidationRequest();
        $request->setTaxability(true);
        $result = $client->Validate($request);
        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax_Logtype::VALIDATE,
            $request,
            $result,
            $this->_storeId,
            $this->getServiceConfig()->getParams()
        );

        return $result;
    }

    /**
     * Get validation request
     * @return ValidateRequest
     */
    private function _getAddressValidationRequest()
    {
        return new ValidateRequest($this->getLocationAddressObject(), TextCase::$Mixed, 0);
    }

    /**
     * Address normalization
     *
     * @param $isAddressNormalizationOn
     * @param ValidateResult $result
     * @return $this
     * @throws \OnePica_AvaTax_Model_Service_Exception_Address
     */
    protected function _addressNormalization($isAddressNormalizationOn, $result)
    {
        if ($isAddressNormalizationOn && $result->getResultCode() == SeverityLevel::$Success) {
            $responseAddress = $result->getValidAddresses();
            $responseAddress = array_pop($responseAddress);
            if ($responseAddress instanceof ValidAddress) {
                $this->_responseAddress = $responseAddress;
                $this->_convertResponseAddress();
            } else {
                throw new OnePica_AvaTax_Model_Service_Exception_Address($this->__('Invalid response address type.'));
            }
        }

        return $this;
    }
}
