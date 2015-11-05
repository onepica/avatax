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
 * Class OnePica_AvaTax_Model_Service_Avatax16_Address
 *
 * @method getService() OnePica_AvaTax_Model_Service_Avatax16
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Address extends OnePica_AvaTax_Model_Service_Avatax16_Abstract
{
    /**
     * Avatax adddres cache tag
     */
    const AVATAX16_SERVICE_CACHE_ADDRESS = 'avatax16_cache_address_';

    /**
     * The Mage Address object
     *
     * @var Mage_Customer_Model_Address_Abstract
     */
    protected $_address = null;

    /**
     * The AvaTax16 Request Address object.
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_locationAddress = null;

    /**
     * The AvaTax16 Request Address object.
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_localeObject = null;

    /**
     * An array of previously checked addresses
     * Example: $_cache[$key] = serialize($resultObjectFromAvalara)
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * Saves the store id
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * OnePica_AvaTax_Model_Service_Avatax16_Address constructor.
     * @param $data
     * @internal param Mage_Customer_Model_Address_Abstract $_address
     */
    public function __construct($data)
    {
        if (isset($data['service_config'])) {
            $this->setServiceConfig($data['service_config']);
        }
        if (isset($data['address'])) {
            $this->setAddress($data['address']);
        }
        parent::_construct();
    }

    /**
     * Class pre-constructor
     */
    public function _construct()
    {
        $addresses = Mage::getSingleton('avatax/session')->getAddresses();
        if (is_array($addresses)) {
            $this->_cache = $addresses;
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
     * Getter for address resolution
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    public function getLocationAddress()
    {
        return $this->_locationAddress;
    }

    /**
     * Setter for address resolution
     * @param OnePica_AvaTax16_Document_Part_Location_Address $addressResolution
     */
    public function setLocationAddress($addressResolution)
    {
        $this->_locationAddress = $addressResolution;
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
        $this->_address = $address;
        $this->_initAddressResolution();
        return $this;
    }

    /**
     * Address getter
     * @return Mage_Customer_Model_Address_Abstract
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    public function getLocaleObject()
    {
        if (is_null($this->_localeObject)){
            $this->_localeObject = new OnePica_AvaTax16_Document_Part_Location_Address();
        }
        return $this->_localeObject;
    }

    /**
     * Init request address object.
     *
     * @return $this
     */
    protected function _initAddressResolution()
    {
        if (is_null($this->getLocationAddress())) {
            $this->setLocationAddress($this->getLocaleObject());
        }
        $address = $this->getAddress()->getStreet();
        if (is_array($address) && isset($address[0])) {
            $address = $address[0];
        }

        $this->getLocationAddress()->setline1($address ? $address : '_');
        $this->getLocationAddress()->setCity($this->getAddress()->getCity());
        $this->getLocationAddress()->setCountry($this->getAddress()->getCountryId());
        $this->getLocationAddress()->setState($this->getAddress()->getRegionId());
        $this->getLocationAddress()->setZipcode($this->getAddress()->getPostcode());
        return $this;
    }

    /**
     * Validates the address with the AvaTax validation API.
     * Returns true on success and an array with an error on failure.
     *
     * @return array|bool
     * @throws OnePica_AvaTax_Model_Service_Exception_Address
     */
    public function validate()
    {
        if (!$this->getAddress()->getId()) {
            throw new OnePica_AvaTax_Model_Service_Exception_Address(
                $this->__('An address must be set before validation.')
            );
        }

        $key = $this->getAddress()->getCacheHashKey();
        if (array_key_exists($key, $this->_cache)) {
            $result = unserialize($this->_cache[$key]);
        } else {
            $result = $this->_sendAddressValidationRequest();
            Mage::getSingleton('avatax/session')->setAvatax16AddressValidation(serialize($result));
        }
//Zend_Debug::dump($result);
        $response = new Varien_Object();
        $response->setHasError($result->getHasError());
        $response->setErrors($result->getErrors());
        $response->setAddress(new Varien_Object($result->getAddress()->toArray()));
        $response->setResolution($result->getResolutionQuality() === 'Rooftop');
        $response->setRequiredProperties($result->getAddress()->getRequiredProperties());
        $response->setExcludedProperties($result->getExcludedProperties());

        return $response;
    }

    /**
     * Validate address
     *
     * @return OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse
     */
    protected function _sendAddressValidationRequest()
    {
        /** @var OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $resolvedAddress */
        $resolvedAddress = $this->getServiceConfig()->getTaxConnection()->resolveSingleAddress($this->getLocationAddress());

        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax_Logtype::VALIDATE,
            $this->getLocationAddress(),
            $resolvedAddress,
            $this->_storeId,
            $this->getServiceConfig()->getData()
        );

        return $resolvedAddress;
    }
}
