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
 * Class OnePica_AvaTax_Model_Action_Validator
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Action_Validator extends OnePica_AvaTax_Model_Action_Abstract
{
    /**
     * The AvaTax Response (Normalized) Address object.
     * This is the normalized Ava address returned by the validation request.
     *
     * @var OnePica_AvaTax16_Document_Part_Location_Address
     */
    protected $_responseAddress = null;

    /**
     * The Mage Address object
     *
     * @var Mage_Customer_Model_Address_Abstract
     */
    protected $_address = null;

    /**
     * Set Address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->_address = $address;

        return $this;
    }

    /**
     * Get service address validator
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return mixed|OnePica_AvaTax_Model_Service_Avatax_Abstract
     */
    public function validate($address)
    {
        $this->setAddress($address);
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $address->getQuote();
        $isAddressValidationOn = $this->_getAddressHelper()->isAddressValidationOn($address, $quote->getStoreId());
        $isAddressNormalizationOn = $this->_getAddressHelper()
            ->isAddressNormalizationOn($address, $quote->getStoreId());
        $isAddressActionable = $this->_getAddressHelper()->isAddressActionable(
            $address,
            $quote->getStoreId(),
            OnePica_AvaTax_Model_Service_Abstract_Config::REGIONFILTER_ALL,
            true
        );
        //if there is no use cases for AvaTax services, return address as valid without doing a lookup
        if (!$isAddressValidationOn && !$isAddressNormalizationOn && !$isAddressActionable) {
            return true;
        }
        if ($address->getPostcode() && $address->getPostcode() != '-') {
            $checkFieldsResult = $this->_checkFields($quote->getStoreId());
            if ($checkFieldsResult) {
                return $checkFieldsResult;
            }
            $this->setStoreId($quote->getStoreId());
            /** @var OnePica_AvaTax_Model_Service_Result_AddressValidate $result */
            $result = $this->_getService()->validate($address);
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
     * Address validation
     *
     * @param int                                                 $isAddressValidationOn
     * @param int                                                 $isAddressActionable
     * @param OnePica_AvaTax_Model_Service_Result_AddressValidate $result
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
                        $this->_addValidateNotice($this->__($message));
                    }
                }

                return true;
            }

            //a valid address isn't required, but Avalara has to say there is
            //enough info to drill down to a tax jurisdiction to calc on
        } elseif (!$isAddressValidationOn && $isAddressActionable) {
            if ($result->isTaxable()) {
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
     * @param int $storeId
     * @return array|null
     */
    protected function _checkFields($storeId = null)
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        if ($session->getPostType() == 'onepage'
            || $session->getPostType() == 'multishipping'
            || Mage::app()->getStore()->isAdmin()
        ) {
            $requiredFields = explode(",", $this->_getConfigHelper()->getFieldRequiredList($storeId));
            $fieldRules = explode(",", $this->_getConfigHelper()->getFieldRule($storeId));
            foreach ($requiredFields as $field) {
                $requiredFlag = 0;
                foreach ($fieldRules as $rule) {
                    if (preg_match("/street\d/", $field)) {
                        $field = "street";
                    }

                    switch ($field) {
                        case 'country':
                            $fieldValue = $this->getAddress()->getCountry();
                            break;
                        case 'region':
                            $fieldValue = $this->getAddress()->getRegion();
                            break;
                        default:
                            $fieldValue = $this->getAddress()->getData($field);
                            break;
                    }

                    if ($fieldValue == $rule || !$fieldValue) {
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
     * @param bool                                                $isAddressNormalizationOn
     * @param OnePica_AvaTax_Model_Service_Result_AddressValidate $result
     * @return $this
     * @throws OnePica_AvaTax_Model_Service_Exception_Address
     */
    protected function _addressNormalization(
        $isAddressNormalizationOn,
        OnePica_AvaTax_Model_Service_Result_AddressValidate $result
    ) {
        if ($isAddressNormalizationOn && (!$result->getHasError() && !$result->getErrors())) {
            if ($result->getAddress() instanceof OnePica_AvaTax_Model_Service_Result_Address) {
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
        $strOriginAddress = $this->getAddress()->format('oneline');

        $street = array($this->getResponseAddress()->getLine1(), $this->getResponseAddress()->getLine2());
        $region = Mage::getModel('directory/region')
            ->loadByCode($this->getResponseAddress()->getState(), $this->getAddress()->getCountryId());

        $this->getAddress()->setStreet($street)
            ->setCity($this->getResponseAddress()->getCity())
            ->setRegionId($region->getId())
            ->setPostcode($this->getResponseAddress()->getPostalCode())
            ->setCountryId($this->getResponseAddress()->getCountry())
            ->save();

        $strResultAddress = $this->getAddress()->format('oneline');
        if ($strOriginAddress != $strResultAddress) {
            $this->getAddress()->setAddressNormalized(true);
        }

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
     *
     * @return Mage_Customer_Model_Address_Abstract
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Get Response Address
     *
     * @return OnePica_AvaTax16_Document_Part_Location_Address
     */
    public function getResponseAddress()
    {
        return $this->_responseAddress;
    }

    /**
     * Set Response Address
     *
     * @param OnePica_AvaTax16_Document_Part_Location_Address $responseAddress
     * @return $this
     */
    public function setResponseAddress($responseAddress)
    {
        $this->_responseAddress = $responseAddress;

        return $this;
    }

    /**
     * Alias to the helper translate method.
     *
     * @return string
     * @skipPublicMethodNaming __
     */
    public function __()
    {
        $args = func_get_args();

        return call_user_func_array(array(Mage::helper('avatax'), '__'), $args);
    }

    /**
     * Add validation notice
     *
     * @param string $message
     * @return $this
     */
    protected function _addValidateNotice($message)
    {
        $notice = Mage::getSingleton('core/message')->notice($message);
        $notice->setIdentifier(OnePica_AvaTax_Helper_Errors::VALIDATION_NOTICE_IDENTIFIER);
        Mage::getSingleton('core/session')->addMessage($notice);

        return $this;
    }
}
