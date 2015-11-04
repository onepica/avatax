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
     * The AvaTax Response (Normalized) Address object.
     * This is the normalized Ava address returned by the validation request.
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_responseAddress = null;

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
        if (isset($data['service_config'])){
            $this->setServiceConfig($data['service_config']);
        }
        if (isset($data['address'])){
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
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    public function getResponseAddress()
    {
        return $this->_responseAddress;
    }

    /**
     * @param OnePica_AvaTax16_Document_Part_Location_Address $responseAddress
     */
    public function setResponseAddress($responseAddress)
    {
        $this->_responseAddress = $responseAddress;
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
     * Init request address object.
     *
     * @return $this
     */
    protected function _initAddressResolution()
    {
        if (is_null($this->getLocationAddress())) {
            $this->setLocationAddress(new OnePica_AvaTax16_Document_Part_Location_Address());
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
     * Sets attributes from the AvaTax Response address on the Mage address.
     *
     * @return $this
     */
    protected function _convertResponseAddress()
    {
        $street = array($this->getResponseAddress()->getLine1(), $this->getResponseAddress()->getLine2());
        $region = Mage::getModel('directory/region')
            ->loadByCode($this->getResponseAddress()->getState(), $this->getAddress()->getCountryId());

        $this->getAddress()->setStreet($street)
            ->setCity($this->getResponseAddress()->getCity())
            ->setRegionId($region->getId())
            ->setPostcode($this->getResponseAddress()->getPostalCode())
            ->setCountryId($this->getResponseAddress()->getCountry())
            ->save()
            ->setAddressNormalized(true);
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


        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $this->getAddress()->getQuote();
        $isAddressValidationOn = $this->_getAddressHelper()
            ->isAddressValidationOn($this->getAddress(), $this->_storeId);
        $isAddressNormalizationOn = $this->_getAddressHelper()
            ->isAddressNormalizationOn($this->getAddress(), $this->_storeId);
        $isAddressActionable = $this->_getAddressHelper()->isAddressActionable($this->getAddress(), $quote->getStoreId());
        //if there is no use cases for AvaTax services, return address as valid without doing a lookup
        if (!$isAddressValidationOn && !$isAddressNormalizationOn && !$isAddressActionable) {
            return true;
        }
        $key = $this->getAddress()->getCacheHashKey();
        if (array_key_exists($key, $this->_cache)) {
            $result = unserialize($this->_cache[$key]);
        } elseif ($this->getAddress()->getPostcode() && $this->getAddress()->getPostcode() != '-') {
            $checkFieldsResult = $this->_checkFields();
            if ($checkFieldsResult) {
                return $checkFieldsResult;
            }
            $result = $this->_sendAddressValidationRequest();
            $this->_cache[$key] = serialize($result);
        } else {
            $errors = array();
            $errors[] = $this->__('Invalid ZIP/Postal Code.');
            return $errors;
        }
        $this->_addressNormalization($isAddressNormalizationOn, $result);

        $addressValidationResult = $this->_addressValidation($isAddressValidationOn, $isAddressActionable, $result);

        Mage::getSingleton('avatax/session')->setAvatax16AddressValidation($addressValidationResult);
        if ($addressValidationResult) {
            return $addressValidationResult;
        }

        return true;
    }

    /**
     * Address validation
     *
     * @param int $isAddressValidationOn
     * @param int $isAddressActionable
     * @param OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $result
     * @return array|bool|null
     */
    protected function _addressValidation($isAddressValidationOn, $isAddressActionable, $result)
    {
        if ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_PREVENT_ORDER) {
            if (!$result->getHasError()) {
                $this->getAddress()->setAddressValidated(true);
                return true;
            } else {
                $errors = array();
                foreach ($result->getErrors() as $message) {
                    $errors[] = $this->__($message);
                }
                return $errors;
            }
        } elseif ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_ALLOW_ORDER) {
            $this->getAddress()->setAddressValidated(true);
            if (!$result->getHasError()) {
                return true;
            } else {
                if (!$this->getAddress()->getAddressNotified()) {
                    $this->getAddress()->setAddressNotified(true);
                    foreach ($result->getErrors() as $message) {
                        Mage::getSingleton('core/session')->addNotice($this->__($message));
                    }
                }
                return true;
            }

            //a valid address isn't required, but Avalara has to say there is
            //enough info to drill down to a tax jurisdiction to calc on
        } elseif (!$isAddressValidationOn && $isAddressActionable) {
            if (count($result->getTaxAuthorities()) > 0) {
                $this->_address->setAddressValidated(true);
                return true;
            } else {
                $errors = array();
                foreach ($result->getErrors() as $message) {
                    $errors[] = $this->__($message);
                }
                return $errors;
            }
        }

        return null;
    }

    /**
     * Check fields
     *
     * @return array|null
     */
    protected function _checkFields()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        if ($session->getPostType() == 'onepage') {
            $requiredFields = explode(",", $this->_getConfigHelper()->getFieldRequiredList());
            $fieldRules = explode(",", $this->_getConfigHelper()->getFieldRule());
            foreach ($requiredFields as $field) {
                $requiredFlag = 0;
                foreach ($fieldRules as $rule) {
                    if (preg_match("/street\d/", $field)) {
                        $field = "street";
                    }
                    if ($field == "country") {
                        $field = "country_id";
                    }
                    if ($this->getAddress()->getData($field) == $rule || !$this->getAddress()->getData($field)) {
                        $requiredFlag = 1;
                    }
                }
                if ($requiredFlag) {
                    $errors = array();
                    $errors[] = $this->__('Invalid ') . $this->__($field);
                    return $errors;
                }
            }
        }

        return null;
    }

    /**
     * Validate address
     *
     * @return ValidateResult
     */
    protected function _sendAddressValidationRequest()
    {
        $taxService = new OnePica_AvaTax16_TaxService($this->getServiceConfig()->getLibConfig());
        /** @var OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $resolvedAddress */
        $resolvedAddress = $taxService->resolveSingleAddress($this->getLocationAddress());

        $this->_log(
            OnePica_AvaTax_Model_Source_Avatax_Logtype::VALIDATE,
            $this->getLocationAddress(),
            $resolvedAddress,
            $this->_storeId,
            $this->getServiceConfig()->getData()
        );

        return $resolvedAddress;
    }

    /**
     * Address normalization
     *
     * @param $isAddressNormalizationOn
     * @param OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $result
     * @return $this
     * @throws \OnePica_AvaTax_Model_Service_Exception_Address
     */
    protected function _addressNormalization($isAddressNormalizationOn, OnePica_AvaTax16_AddressResolution_ResolveSingleAddressResponse $result)
    {
        if ($isAddressNormalizationOn && !$result->getHasError()) {
            if ($result->getAddress() instanceof OnePica_AvaTax16_Document_Part_Location_Address) {
                $this->setResponseAddress($result->getAddress());
                $this->_convertResponseAddress();
            } else {
                throw new OnePica_AvaTax_Model_Service_Exception_Address($this->__('Invalid response address type.'));
            }
        }

        return $this;
    }
}
