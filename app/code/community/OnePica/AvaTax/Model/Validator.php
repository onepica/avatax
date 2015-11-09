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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Model_Validator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Validator extends Mage_Core_Model_Factory
{

    /**
     * The AvaTax Response (Normalized) Address object.
     * This is the normalized Ava address returned by the validation request.
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_responseAddress = null;

    /**
     * Service
     * @var OnePica_AvaTax_Model_Service_Abstract
     */
    protected $_service;

    /**
     * The Mage Address object
     *
     * @var Mage_Customer_Model_Address_Abstract
     */
    protected $_address = null;

    /**
     * Class constructor
     * @param array $params
     * @throws OnePica_AvaTax_Exception
     */
    public function __construct($params = array())
    {
        $activeService = $this->_getConfigHelper()->getActiveService();
        $this->_service = Mage::getSingleton('avatax/service')->factory($activeService, $params);
    }

    /**
     * Set Address
     * @param Mage_Customer_Model_Address_Abstract $address
     */
    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * Get Service
     *
     * return OnePica_AvaTax_Model_Service_Abstract
     */
    protected function _getService()
    {
        return $this->_service;
    }

    /**
     * Get service address validator
     *
     * @return mixed|OnePica_AvaTax_Model_Service_Avatax_Abstract
     */
    public function validate($address)
    {
        $this->setAddress($address);
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $address->getQuote();
        $isAddressValidationOn = $this->_getAddressHelper()->isAddressValidationOn($address, $quote->getStoreId());
        $isAddressNormalizationOn = $this->_getAddressHelper()->isAddressNormalizationOn($address, $quote->getStoreId());
        $isAddressActionable = $this->_getAddressHelper()->isAddressActionable($address, $quote->getStoreId());
        //if there is no use cases for AvaTax services, return address as valid without doing a lookup
        if (!$isAddressValidationOn && !$isAddressNormalizationOn && !$isAddressActionable) {
            return true;
        }
        if ($address->getPostcode() && $address->getPostcode() != '-') {
            $checkFieldsResult = $this->_checkFields();
            if ($checkFieldsResult) {
                return $checkFieldsResult;
            }
            /** @var Varien_Object $result */
            $result = $this->_getService()->getAddressValidator($address)->validate();
        } else {
            $errors = array();
            $errors[] = $this->__('Invalid ZIP/Postal Code.');
            return $errors;
        }

        $this->_addressNormalization($isAddressNormalizationOn, $result);
        $addressValidationResult = $this->_addressValidation($isAddressValidationOn, $isAddressActionable, $result);
        if ($addressValidationResult) {
            return $addressValidationResult;
        }

        return true;
    }

    /**
     * Get config helper
     * @return OnePica_AvaTax_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avatax/config');
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
        $errors = array();
        if ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_PREVENT_ORDER) {
            if (!$result->getHasError() && $result->getResolution()) {
                $this->getAddress()->setAddressValidated(true);
                return true;
            } else {
                foreach ($result->getErrors() as $message) {
                    $errors[] = $this->__($message);
                }
                return $errors;
            }
        } elseif ($isAddressValidationOn == OnePica_AvaTax_Model_Source_Addressvalidation::ENABLED_ALLOW_ORDER) {
            $this->getAddress()->setAddressValidated(true);
            if (!$result->getHasError() && $result->getResolution()) {
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
            if ($result->getResolution()) {
                $this->getAddress()->setAddressValidated(true);
                return true;
            } else {
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
     * Address normalization
     *
     * @param $isAddressNormalizationOn
     * @param Varien_Object $result
     * @return $this
     * @throws OnePica_AvaTax_Model_Service_Exception_Address
     */
    protected function _addressNormalization($isAddressNormalizationOn, Varien_Object $result)
    {
        if ($isAddressNormalizationOn && !$result->getHasError()) {
            if ($result->getAddress() instanceof Varien_Object) {
                $this->setResponseAddress($result->getAddress());
                $this->_convertResponseAddress();
            } else {
                throw new OnePica_AvaTax_Model_Service_Exception_Address($this->__('Invalid response address type.'));
            }
        }

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
     * Returns the AvaTax helper.
     *
     * @return OnePica_AvaTax_Helper_Address
     */
    protected function _getAddressHelper()
    {
        return Mage::helper('avatax/address');
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
     * Alias to the helper translate method.
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        return call_user_func_array(array(Mage::helper('avatax'), '__'), $args);
    }
}
