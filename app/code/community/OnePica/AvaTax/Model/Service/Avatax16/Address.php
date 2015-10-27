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
 * @method OnePica_AvaTax_Model_Service_Avatax16 getService()
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Service_Avatax16_Address extends OnePica_AvaTax_Model_Service_Avatax_Abstract
{
    /**
     * Avatax adddres cache tag
     */
    const AVATAX_SERVICE_CACHE_ADDRESS = 'avatax16_cache_address_';

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
    protected $_address = null;

    /**
     * The AvaTax16 Request Address object.
     *
     * @var OnePica_AvaTax16_AddressResolution
     */
    protected $_addressResolution = null;

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
    public function __construct()
    {
        parent::__construct();
        $addresses = Mage::getSingleton('avatax/session')->getAddresses();
        if (is_array($addresses)) {
            $this->_cache = $addresses;
        }
    }

    /**
     * Class pre-constructor
     */
    protected function _construct()
    {
        $this->addCacheTag(array(
            self::AVATAX_SERVICE_CACHE_ADDRESS
        ));
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
        $this->_address = $address;
        $this->_initAddressResolution();
        $this->addCacheTag(array($this->_address->getId()));
        return $this;
    }

    /**
     * Init request address object.
     *
     * @return $this
     */
    protected function _initAddressResolution()
    {

        if (is_null($this->getAddressResolution())) {
            $this->setAddressResolution(new OnePica_AvaTax16_AddressResolution($this->getService()->getConfig()));
        }

        return $this;
    }

    /**
     * Getter for address resolution
     * @return OnePica_AvaTax16_AddressResolution
     */
    public function getAddressResolution()
    {
        return $this->_addressResolution;
    }

    /**
     * Setter for address resolution
     * @param OnePica_AvaTax16_AddressResolution $addressResolution
     */
    public function setAddressResolution($addressResolution)
    {
        $this->_addressResolution = $addressResolution;
    }

    /**
     * Sets attributes from the AvaTax Response address on the Mage address.
     *
     * @return $this
     */
    protected function _convertResponseAddress()
    {
        $street = array($this->_responseAddress->getLine1(), $this->_responseAddress->getLine2());
        $region = Mage::getModel('directory/region')
            ->loadByCode($this->_responseAddress->getRegion(), $this->_address->getCountryId());

        $this->_address->setStreet($street)
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

        $result = $this->_loadCache();
        if ($result !== false) {
            return boolval($result);
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

        //lookup in AvaTax (with caching)
        $key = $this->getAddress()->getCacheHashKey();

//        if (array_key_exists($key, $this->_cache)) {
//            $result = unserialize($this->_cache[$key]);
//        } else
            if ($this->getAddress()->getPostcode() && $this->getAddress()->getPostcode() != '-') {
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
        $this->_saveCache($addressValidationResult);
        if ($addressValidationResult) {
            return $addressValidationResult;
        }

        return true;
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
     * Address validation
     *
     * @param int $isAddressValidationOn
     * @param int $isAddressActionable
     * @param ValidateResult $result
     * @return array|bool|null
     */
    protected function _addressValidation($isAddressValidationOn, $isAddressActionable, $result)
    {
        if ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_PREVENT_ORDER) {
            if ($result->getResultCode() == SeverityLevel::$Success) {
                $this->_address->setAddressValidated(true);
                return true;
            } else {
                $errors = array();
                foreach ($result->getMessages() as $message) {
                    $errors[] = $this->__($message->getSummary());
                }
                return $errors;
            }
        } elseif ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_ALLOW_ORDER) {
            $this->_address->setAddressValidated(true);
            if ($result->getResultCode() == SeverityLevel::$Success) {
                return true;
            } else {
                if (!$this->_address->getAddressNotified()) {
                    $this->_address->setAddressNotified(true);
                    foreach ($result->getMessages() as $message) {
                        Mage::getSingleton('core/session')->addNotice($this->__($message->getSummary()));
                    }
                }
                return true;
            }

            //a valid address isn't required, but Avalara has to say there is
            //enough info to drill down to a tax jurisdiction to calc on
        } elseif (!$isAddressValidationOn && $isAddressActionable) {
            if ($result->isTaxable()) {
                $this->_address->setAddressValidated(true);
                return true;
            } else {
                $errors = array();
                foreach ($result->getMessages() as $message) {
                    $errors[] = $this->__($message->getSummary());
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
                    if ($this->_address->getData($field) == $rule || !$this->_address->getData($field)) {
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
        $result = $this->getAddressResolution()->resolveSingleAddress($this->getAddress());
//Zend_Debug::dump($result);die;
        /** @var OnePica_AvaTax_Model_Config $config */
//        $client = $config->getAddressConnection();
//        $request = new ValidateRequest($this->_addressResolution, TextCase::$Mixed, 0);
//        $request->setTaxability(true);
//        $result = $client->Validate($request);
//        $this->_log(
//            OnePica_AvaTax_Model_Source_Logtype::VALIDATE,
//            $request,
//            $result,
//            $this->_storeId,
//            $config->getParams()
//        );

        return $result;
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
