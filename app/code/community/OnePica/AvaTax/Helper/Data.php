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
 * The base AvaTax Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Xml path to taxable_country config
     */
    const XML_PATH_TO_TAX_AVATAX_TAXABLE_COUNTRY = 'tax/avatax/taxable_country';

    /**
     * Xml path to region_filter_mode
     */
    const XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE = 'tax/avatax/region_filter_mode';

    /**
     * Identifier for error message
     */
    const CALCULATE_ERROR_MESSAGE_IDENTIFIER = 'avatax_calculate_error';

    /**
     * Check if avatax extension is enabled
     *
     * @param null|bool|int|Mage_Core_Model_Store $store $store
     * @return bool
     */
    public function isAvataxEnabled($store = null)
    {
        return ($this->_getConfig('action', $store) != OnePica_AvaTax_Model_Config::ACTION_DISABLE);
    }

    /**
     * Gets the documenation url
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'http://www.onepica.com/magento-extensions/avatax/';
    }

    /**
     * Loads a class from the AvaTax library.
     *
     * @param string $className
     * @return OnePica_AvaTax_Helper_Data
     */
    public function loadClass($className)
    {
        $classFile = $this->getLibPath() . DS . 'classes' . DS . $className . '.class.php';
        require_once $classFile;
        return $this;
    }

    /**
     * Load functions required to work with Avalara API
     *
     * @return $this
     */
    public function loadFunctions()
    {
        $functionsFile = $this->getLibPath() . DS . 'functions.php';
        require_once $functionsFile;
        return $this;
    }

    /**
     * Returns the path to the etc directory.
     *
     * @return string
     */
    public function getEtcPath ()
    {
        return dirname(dirname(__FILE__)) . DS . 'etc';
    }

    /**
     * Returns the path to the AvaTax SDK lib directory.
     *
     * @return string
     */
    public function getLibPath()
    {
        return Mage::getBaseDir('lib') . DS . 'AvaTax';
    }

    /**
     * Returns a config value from the admin.
     *
     * @param string $path
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    protected function _getConfig ($path, $store = null)
    {
        return Mage::getSingleton('avatax/config')->getConfig($path, $store);
    }

    /**
     * Returns the logging level
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return int
     */
    public function getLogMode($store = null)
    {
        return $this->_getConfig('log_status', $store);
    }

    /**
     * Returns the logging type
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getLogType($store = null)
    {
        return explode(",", $this->_getConfig('log_type_list', $store));
    }

    /**
     * Returns shipping line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getShippingSku($store = null)
    {
        return $this->_getConfig('shipping_sku', $store);
    }

    /**
     * Returns giftwraporder line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getGwOrderSku($store = null)
    {
        return $this->_getConfig('gw_order_sku', $store);
    }

    /**
     * Returns giftwrapitems line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getGwItemsSku($store = null)
    {
        return $this->_getConfig('gw_items_sku', $store);
    }

    /**
     * Returns giftwrapprintedcard line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getGwPrintedCardSku($store = null)
    {
        return $this->_getConfig('gw_printed_card_sku', $store);
    }

    /**
     * Returns shipping line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getSalesPersonCode($store = null)
    {
        return $this->_getConfig('sales_person_code', $store);
    }

    /**
     * Returns attribute code for the location code to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getLocationCode($store = null)
    {
        return $this->_getConfig('location_code', $store);
    }

    /**
     * Returns attribute code for the reference code 1 to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getRef1AttributeCode($store = null)
    {
        return $this->_getConfig('line_ref1_code', $store);
    }

    /**
     * Returns attribute code for the reference code 2 to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getRef2AttributeCode($store = null)
    {
        return $this->_getConfig('line_ref2_code', $store);
    }

    /**
     * Returns the positive adjustment identifier to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getPositiveAdjustmentSku($store = null)
    {
        return $this->_getConfig('adjustment_positive_sku', $store);
    }

    /**
     * Returns the negative adjustment identifier to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getNegativeAdjustmentSku($store = null)
    {
        return $this->_getConfig('adjustment_negative_sku', $store);
    }

    /**
     * Returns the required field list
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getFieldRequiredList($store = null)
    {
        return $this->_getConfig('field_required_list', $store);
    }

    /**
     * Returns the rules for field
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getFieldRule($store = null)
    {
        return $this->_getConfig('field_rule', $store);
    }

    /**
     * Returns full stop on error
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function fullStopOnError($store = null)
    {
        return (bool)$this->_getConfig('error_full_stop', $store);
    }

    /**
     * Get address validation countries
     *
     * @return array
     */
    public function getAddressValidationCountries()
    {
        return explode(',', $this->_getConfig('address_validation_countries'));
    }

    /**
     * Adds error message if there is an error
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function addErrorMessage($store = null)
    {
        $message = $this->getErrorMessage($store);
        if (Mage::app()->getStore()->isAdmin()) {
            /** @var Mage_Adminhtml_Model_Session_Quote $session */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /** @var Mage_Checkout_Model_Session $session */
            $session = Mage::getSingleton('checkout/session');
        }

        $messages = $session->getMessages();
        if (!$messages->getMessageByIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER)) {
            /** @var Mage_Core_Model_Message_Error $error */
            $error = Mage::getSingleton('core/message')->error($message);
            $error->setIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER);
            $session->addMessage($error);
        }
        return $message;
    }

    /**
     * Remove error message
     *
     * @return $this
     */
    public function removeErrorMessage()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            /** @var Mage_Adminhtml_Model_Session_Quote $session */
            $session = Mage::getSingleton('adminhtml/session_quote');
        } else {
            /** @var Mage_Checkout_Model_Session $session */
            $session = Mage::getSingleton('checkout/session');
        }
        /** @var Mage_Core_Model_Message_Collection $messages */
        $messages = $session->getMessages();
        $messages->deleteMessageByIdentifier(self::CALCULATE_ERROR_MESSAGE_IDENTIFIER);
        return $this;
    }

    /**
     * Gets error message
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getErrorMessage($store = null)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return $this->_getConfig('error_backend_message', $store);
        } else {
            return $this->_getConfig('error_frontend_message', $store);
        }
    }

    /**
     * Is AvaTax disabled.
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isAvaTaxDisabled()
    {
        $websiteId = Mage::app()->getRequest()->get('website');
        $storeId = Mage::app()->getRequest()->get('store');

        if ($websiteId && !$storeId) {
            return !(bool)Mage::app()->getWebsite($websiteId)->getConfig('tax/avatax/action');
        }

        return !(bool)Mage::getStoreConfig('tax/avatax/action', $storeId);
    }

    /**
     * Does any store have this extension disabled?
     *
     * @return bool
     */
    public function isAnyStoreDisabled()
    {
        $disabled = false;
        $storeCollection = Mage::app()->getStores();

        foreach ($storeCollection as $store) {
            $disabled |= Mage::getStoreConfig('tax/avatax/action', $store->getId())
                == OnePica_AvaTax_Model_Config::ACTION_DISABLE;
        }

        return $disabled;
    }

    /**
     * Determines if address validation is enabled
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @param int $storeId
     * @return bool
     */
    public function isAddressValidationOn($address, $storeId)
    {
        if (!$this->isAddressActionable($address, $storeId, OnePica_AvaTax_Model_Config::REGIONFILTER_ALL, true)) {
            return false;
        }
        return Mage::getStoreConfig('tax/avatax/validate_address', $storeId);
    }

    /**
     * Determines if address normalization is enabled
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @param int $storeId
     * @return bool
     */
    public function isAddressNormalizationOn($address, $storeId)
    {
        if (!$this->isAddressActionable($address, $storeId, OnePica_AvaTax_Model_Config::REGIONFILTER_ALL, true)) {
            return false;
        }
        return Mage::getStoreConfig('tax/avatax/normalize_address', $storeId);
    }

    /**
     * Determines if the address should be filtered
     *
     * @param OnePica_AvaTax_Model_Sales_Quote_Address $address
     * @param int                         $storeId
     * @param int                         $filterMode
     * @param bool                        $isAddressValidation
     * @return bool
     */
    public function isAddressActionable($address, $storeId, $filterMode = OnePica_AvaTax_Model_Config::REGIONFILTER_ALL,
        $isAddressValidation = false
    ) {
        $filter = false;

        if (Mage::getStoreConfig('tax/avatax/action', $storeId) == OnePica_AvaTax_Model_Config::ACTION_DISABLE) {
            return false;
        }

        if ($this->getRegionFilterModByStore($storeId) >= $filterMode) {
            $filter = $this->_getFilterRegion($address, $storeId);
        }

        if ($isAddressValidation && $filter
            && ((int)$this->getRegionFilterModByStore($storeId) !== OnePica_AvaTax_Model_Config::REGIONFILTER_ALL)
        ) {
            $filter = false;
        }

        if (!in_array($address->getCountryId(), $this->getTaxableCountryByStore($storeId))) {
            $filter = 'country';
        }

        if ($isAddressValidation && !$filter
            && !in_array($address->getCountryId(), $this->getAddressValidationCountries())
        ) {
            $filter = 'country';
        }

        if ($filter && $this->getLogMode($storeId)) {
            $filterLog = Mage::getSingleton('avatax/session')->getFilterLog();
            if (!is_array($filterLog)) {
                $filterLog = array();
            }
            $key = $address->getCacheHashKey();

            //did we already log this filtered address?
            if (!in_array($key, $filterLog)) {
                $filterLog[] = $key;
                Mage::getSingleton('avatax/session')->setFilterLog($filterLog);

                $type = ($filterMode == OnePica_AvaTax_Model_Config::REGIONFILTER_TAX) ?
                    'tax_calc' : 'tax_calc|address_opts';
                Mage::getModel('avatax_records/log')
                    ->setStoreId($storeId)
                    ->setLevel('Success')
                    ->setType('Filter')
                    ->setRequest(print_r($address->debug(), true))
                    ->setResult('filter: ' . $filter . ', type: ' . $type)
                    ->save();
            }
        }

        return $filter ? false : true;
    }

    /**
     * Get region filter
     *
     * @param Mage_Customer_Model_Address $address
     * @param int                         $storeId
     * @return string|bool
     */
    protected function _getFilterRegion($address, $storeId)
    {
        $filter = false;
        $regionFilters = explode(',', Mage::getStoreConfig('tax/avatax/region_filter_list', $storeId));
        $entityId = $address->getRegionId() ?: $address->getCountryId();
        if (!in_array($entityId, $regionFilters)) {
            $filter = 'region';
        }
        return $filter;
    }

    /**
     * Get taxable country by store
     *
     * @param int $storeId
     * @return array
     */
    public function getTaxableCountryByStore($storeId = null)
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_TAXABLE_COUNTRY, $storeId));
    }

    /**
     * Get taxable country by website
     *
     * @param int $websiteId
     * @return array
     */
    public function getTaxableCountryByWebSite($websiteId)
    {
        return explode(',', Mage::app()
            ->getWebsite($websiteId)
            ->getConfig(self::XML_PATH_TO_TAX_AVATAX_TAXABLE_COUNTRY)
        );
    }

    /**
     * Get taxable country by current scope
     *
     * Used in admin panel
     *
     * @return array
     */
    public function getTaxableCountryByCurrentScope()
    {
        $websiteId = Mage::app()->getRequest()->get('website');
        $storeId = Mage::app()->getRequest()->get('store');
        if ($websiteId && !$storeId) {
            return $this->getTaxableCountryByWebSite($websiteId);
        }
        return $this->getTaxableCountryByStore($storeId);
    }

    /**
     * Get region filter mod by store
     *
     * @param null|int $storeId
     * @return int
     * @internal param int $store
     */
    public function getRegionFilterModByStore($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE, $storeId);
    }

    /**
     * Get region filter mod by website
     *
     * @param int $websiteId
     * @return int
     * @throws \Mage_Core_Exception
     */
    public function getRegionFilterModByWebsite($websiteId)
    {
        return Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE);
    }

    /**
     * Get region filter mode by current scope
     *
     * @throws \Mage_Core_Exception
     * @return int
     */
    public function getRegionFilterModByCurrentScope()
    {
        $websiteId = Mage::app()->getRequest()->get('website');
        $storeId = Mage::app()->getRequest()->get('store');

        if ($websiteId && !$storeId) {
            return $this->getRegionFilterModByWebsite($websiteId);
        }

        return $this->getRegionFilterModByStore($storeId);
    }

    /**
     * Determines if the object (quote, invoice, or credit memo) should use AvaTax services
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param Mage_Sales_Model_Quote_Address $shippingAddress
     * @return bool
     */
    public function isObjectActionable($object, $shippingAddress = null)
    {
        $storeId = $object->getStore()->getId();

        //is action enabled?
        $action = $object->getOrder() ?
            OnePica_AvaTax_Model_Config::ACTION_CALC_SUBMIT : OnePica_AvaTax_Model_Config::ACTION_CALC;
        if (Mage::getStoreConfig('tax/avatax/action', $storeId) < $action) {
            return false;
        }

        if (!$shippingAddress) {
            $shippingAddress = $object->getShippingAddress();
        }
        if (!$shippingAddress) {
            $shippingAddress = $object->getBillingAddress();
        }

        //is the region filtered?
        if (!$this->isAddressActionable($shippingAddress, $storeId, OnePica_AvaTax_Model_Config::REGIONFILTER_TAX)) {
            return false;
        }

        return true;
    }

    /**
     * Round up
     *
     * @param float $value
     * @param int   $precision
     * @return float
     */
    public function roundUp($value, $precision)
    {
        $fact = pow(10, $precision);

        return ceil($fact * $value) / $fact;
    }

    /**
     * Get UPC attributeCode
     *
     * @param int $storeId
     * @return string
     */
    public function getUpcAttributeCode($storeId = null)
    {
        if (!(bool)$this->_getConfig('upc_check_status', $storeId)) {
            return '';
        }
        return (string)$this->_getConfig('upc_attribute_code', $storeId);
    }
}
