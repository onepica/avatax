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
class OnePica_AvaTax_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Path to customer code format
     */
    const PATH_TAX_AVATAX_CUST_CODE_FORMAT = 'tax/avatax/cust_code_format';

    /**
     * Path to customer code format attribute
     */
    const PATH_TAX_AVATAX_CUST_CODE_FORMAT_ATTRIBUTE = 'tax/avatax/cust_code_format_attribute';

    /**
     * Path to full stop on error
     */
    const XML_PATH_TO_TAX_AVATAX_ERROR_STOP = 'tax/avatax/error_full_stop';

    /**
     * Path to shipping line item faked sku
     */
    const XML_PATH_TO_TAX_AVATAX_SHIPPING_SKU = 'tax/avatax/shipping_sku';

    /**
     * Path to giftwraporder line item faked sku
     */
    const XML_PATH_TO_TAX_AVATAX_GW_ORDER_SKU = 'tax/avatax/gw_order_sku';

    /**
     * Path to giftwrapitems line item faked sku
     */
    const XML_PATH_TO_TAX_AVATAX_GW_ITEMS_SKU = 'tax/avatax/gw_items_sku';

    /**
     * Path to giftwrapprintedcard line item faked sku
     */
    const XML_PATH_TO_TAX_AVATAX_GW_PRINTED_CARD_SKU = 'tax/avatax/gw_printed_card_sku';

    /**
     * Path to shipping line item faked sku
     */
    const XML_PATH_TO_TAX_AVATAX_SALES_PERSON_CODE = 'tax/avatax/sales_person_code';

    /**
     * Path to attribute code for the location code to send to Avalara
     */
    const XML_PATH_TO_TAX_AVATAX_LOCATION_CODE = 'tax/avatax/location_code';

    /**
     * Path to the positive adjustment identifier to send to Avalara
     */
    const XML_PATH_TO_TAX_AVATAX_ADJUSTMENT_POSITIVE_SKU = 'tax/avatax/adjustment_positive_sku';

    /**
     * Path to the negative adjustment identifier to send to Avalara
     */
    const XML_PATH_TO_TAX_AVATAX_ADJUSTMENT_NEGATIVE_SKU = 'tax/avatax/adjustment_negative_sku';

    /**
     * Path to the required field list
     */
    const XML_PATH_TO_TAX_AVATAX_FIELD_REQUIRED_LIST = 'tax/avatax/field_required_list';

    /**
     * Path to the rules for field
     */
    const XML_PATH_TO_TAX_AVATAX_FIELD_RULE = 'tax/avatax/field_rule';

    /**
     * Path to the rules for url
     */
    const XML_PATH_TO_TAX_AVATAX_URL = 'tax/avatax/url';

    /**
     * Path to the rules for url
     */
    const XML_PATH_TO_TAX_AVATAX_ACCOUNT = 'tax/avatax/account';

    /**
     * Path to the rules for url
     */
    const XML_PATH_TO_TAX_AVATAX_KEY = 'tax/avatax/license';

    /**
     * Path to the rules for url
     */
    const XML_PATH_TO_TAX_AVATAX_COMPANY_CODE = 'tax/avatax/company_code';

    /**
     * Path to tax detail level
     */
    const XML_PATH_TO_TAX_AVATAX_DETAIL_LEVEL = 'tax/avatax/detail_level';

    /**
     * Path to attribute code for the reference code 1 to send to Avalara
     */
    const XML_PATH_TO_TAX_AVATAX_LINE_REF1_CODE = 'tax/avatax/line_ref1_code';

    /**
     * Path to attribute code for the reference code 2 to send to Avalara
     */
    const XML_PATH_TO_TAX_AVATAX_LINE_REF2_CODE = 'tax/avatax/line_ref2_code';

    /**
     * Path to is AvaTax disabled.
     */
    const XML_PATH_TO_TAX_AVATAX_ACTION = 'tax/avatax/action';

    /**
     * Path to error in backend massage
     */
    const CALCULATE_ERROR_BACKEND_MESSAGE = 'tax/avatax/error_backend_message';

    /**
     * Path to error frontend message
     */
    const CALCULATE_ERROR_FRONTEND_MESSAGE = 'tax/avatax/error_frontend_message';

    /**
     * Path to is address normalization on
     */
    const XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS = 'tax/avatax/normalize_address';

    /**
     * Path to is customer can disable address normalization
     */
    const XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER = 'tax/avatax/normalize_address_disabler';

    /**
     * Path to normalize disabler label
     */
    const XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER_LABEL = 'tax/avatax/normalize_address_disabler_label';

    /**
     * Path to normalize disabler please wait label
     */
    const XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER_PLEASE_WAIT_LABEL = 'tax/avatax/normalize_address_disabler_please_wait_label';

    /**
     * Path to is address validate
     */
    const XML_PATH_TO_TAX_AVATAX_VALIDATE_ADDRESS = 'tax/avatax/validate_address';

    /**
     * Path to address validation countries
     */
    const XML_PATH_TO_TAX_AVATAX_ADDRESS_VALIDATION_COUNTRIES = 'tax/avatax/address_validation_countries';

    /**
     * Xml path to region_filter_mode
     */
    const XML_PATH_TO_TAX_AVATAX_REGION_FILTER_LIST = 'tax/avatax/region_filter_list';

    /**
     * Xml path to region_filter_mode
     */
    const XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE = 'tax/avatax/region_filter_mode';

    /**
     * Xml path to taxable_country config
     */
    const XML_PATH_TO_TAX_AVATAX_TAXABLE_COUNTRY = 'tax/avatax/taxable_country';

    /**
     * Path to the logging type
     */
    const XML_PATH_TO_TAX_AVATAX_LOG_TYPE_LIST = 'tax/avatax/log_type_list';

    /**
     * Path to is AvaTax disabled.
     */
    const XML_PATH_TO_TAX_AVATAX_LOG_STATUS = 'tax/avatax/log_status';

    /**
     * Path to advanced logging with related info
     */
    const XML_PATH_TO_TAX_AVATAX_LOG_ADVANCED = 'tax/avatax/log_advanced';

    /**
     * Path to active avatax service
     */
    const XML_PATH_TO_TAX_AVATAX_ACTIVE_SERVICE = 'tax/avatax/active_service';

    /**
     * Path to active avatax service
     */
    const XML_PATH_TO_TAX_AVATAX_ONEPAGE_NORMALIZE_MESSAGE = 'tax/avatax/onepage_normalize_message';

    /**
     * Path to active avatax service
     */
    const XML_PATH_TO_SHIPPING_ORIGIN_COUNTRY_ID = 'shipping/origin/country_id';

    /**
     * Path to upc status
     */
    const XML_PATH_TO_AVATAX_UPC_CHECK_STATUS = 'tax/avatax/upc_check_status';

    /**
     * Path to avatax_classes shipping_tax_code
     */
    const XML_PATH_TO_AVATAX_SHIPPING_TAX_CODE = 'tax/avatax/shipping_tax_code';

    /**
     * Path to upc attribute code
     */
    const PATH_TO_AVATAX_UPC_ATTRIBUTE_CODE = 'tax/avatax/upc_attribute_code';

    /**
     * Path to avatax16 address validation message
     */
    const PATH_TO_AVATAX16_ADDRESS_VALIDATION_MESSAGE = 'tax/avatax/avatax16_onepage_validate_address_message';

    /**#@+
     * Service types
     */
    const AVATAX16_SERVICE_TYPE = 'avatax16';
    const AVATAX_SERVICE_TYPE   = 'avatax';
    /**#@-*/

    /**
     * Returns full stop on error
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function fullStopOnError($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ERROR_STOP, $store);
    }

    /**
     * Returns shipping line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getShippingSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_SHIPPING_SKU, $store);
    }

    /**
     * Returns service url
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceUrl($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_URL, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ACCOUNT, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getServiceKey($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_KEY, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getCompanyCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_COMPANY_CODE, $store);
    }

    /**
     * Returns service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getDetailLevel($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_DETAIL_LEVEL, $store);
    }

    /**
     * Returns giftwraporder line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getGwOrderSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_GW_ORDER_SKU, $store);
    }

    /**
     * Returns giftwrapitems line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getGwItemsSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_GW_ITEMS_SKU, $store);
    }

    /**
     * Returns giftwrapprintedcard line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getGwPrintedCardSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_GW_PRINTED_CARD_SKU, $store);
    }

    /**
     * Returns shipping line item faked sku
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getSalesPersonCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_SALES_PERSON_CODE, $store);
    }

    /**
     * Returns attribute code for the location code to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getLocationCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LOCATION_CODE, $store);
    }

    /**
     * Returns the positive adjustment identifier to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getPositiveAdjustmentSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ADJUSTMENT_POSITIVE_SKU, $store);
    }

    /**
     * Returns the negative adjustment identifier to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getNegativeAdjustmentSku($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ADJUSTMENT_NEGATIVE_SKU, $store);
    }

    /**
     * Returns the required field list
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFieldRequiredList($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_FIELD_REQUIRED_LIST, $store);
    }

    /**
     * Returns the rules for field
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFieldRule($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_FIELD_RULE, $store);
    }

    /**
     * Returns attribute code for the reference code 1 to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getRef1AttributeCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LINE_REF1_CODE, $store);
    }

    /**
     * Returns attribute code for the reference code 2 to send to Avalara
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getRef2AttributeCode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LINE_REF2_CODE, $store);
    }

    /**
     * Get
     */
    public function get()
    {
    }

    /**
     * Returns the path to the etc directory.
     *
     * @return string
     */
    public function getEtcPath()
    {
        return dirname(dirname(__FILE__)) . DS . 'etc';
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
        $storeId   = Mage::app()->getRequest()->get('store');

        if ($websiteId && !$storeId) {
            return !(bool)Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_TO_TAX_AVATAX_ACTION);
        }

        return !(bool)Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ACTION, $storeId);
    }

    /**
     * Get avatax status
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getStatusServiceAction($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ACTION, $storeId);
    }

    /**
     * Get backend message
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getErrorBackendMessage($storeId = null)
    {
        return Mage::getStoreConfig(self::CALCULATE_ERROR_BACKEND_MESSAGE, $storeId);
    }

    /**
     * Get frontend message
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getErrorFrontendMessage($storeId = null)
    {
        return Mage::getStoreConfig(self::CALCULATE_ERROR_FRONTEND_MESSAGE, $storeId);
    }

    /**
     * Get normalize address
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getNormalizeAddress($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS, $storeId);
    }

    /**
     * Get normalize address disabler
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getNormalizeAddressDisabler($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER, $storeId);
    }

    /**
     * Get normalize address disabler label
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getNormalizeAddressDisablerLabel($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER_LABEL, $storeId);
    }

    /**
     * Get normalize address disabler please wait label
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getNormalizeAddressDisablerPleaseWaitLabel($storeId = null)
    {
        return Mage::getStoreConfig(
            self::XML_PATH_TO_TAX_AVATAX_NORMALIZE_ADDRESS_DISABLER_PLEASE_WAIT_LABEL,
            $storeId
        );
    }

    /**
     * Get validate address
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getValidateAddress($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_VALIDATE_ADDRESS, $storeId);
    }

    /**
     * Get address validation countries
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getAddressValidationCountries($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ADDRESS_VALIDATION_COUNTRIES, $storeId);
    }

    /**
     * Get region filter fist
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getRegionFilterList($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_REGION_FILTER_LIST, $storeId);
    }

    /**
     * Get region filter fist
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getLogTypeList($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LOG_TYPE_LIST, $storeId);
    }

    /**
     * Get region filter mode
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getRegionFilterMode($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE, $storeId);
    }

    /**
     * Get region filter mode
     *
     * @param int|Mage_Core_Model_Store $websiteId
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getConfigRegionFilterModByWebsite($websiteId = null)
    {
        return Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_TO_TAX_AVATAX_REGION_FILTER_MODE);
    }

    /**
     * Get taxable country
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getTaxableCountry($storeId = null)
    {
        return  Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_TAXABLE_COUNTRY, $storeId);
    }

    /**
     * Returns the logging level
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function getConfigLogMode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LOG_STATUS, $store);
    }

    /**
     * Is enabled the advanced logging with related info
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function getConfigAdvancedLog($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_LOG_ADVANCED, $store);
    }

    /**
     * Get active avatax service
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function getActiveService($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ACTIVE_SERVICE, $store);
    }

    /**
     * Get shipping origin country_id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function getShippingOriginCountryId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_SHIPPING_ORIGIN_COUNTRY_ID, $store);
    }

    /**
     * Get onepage normalize message
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return int
     */
    public function getOnepageNormalizeMessage($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_TAX_AVATAX_ONEPAGE_NORMALIZE_MESSAGE, $store);
    }

    /**
     * Get UPC attributeCode
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return string
     */
    public function getUpcAttributeCode($storeId = null)
    {
        if (!(bool)Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_UPC_CHECK_STATUS, $storeId)) {
            return '';
        }

        return (string)Mage::getStoreConfig(self::PATH_TO_AVATAX_UPC_ATTRIBUTE_CODE, $storeId);
    }

    /**
     * Get customer code format
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCustomerCodeFormat($store)
    {
        return (string)Mage::getStoreConfig(self::PATH_TAX_AVATAX_CUST_CODE_FORMAT, $store);
    }

    /**
     * Get customer code format attribute
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCustomerCodeFormatAttribute($store = null)
    {
        return (string)Mage::getStoreConfig(self::PATH_TAX_AVATAX_CUST_CODE_FORMAT_ATTRIBUTE, $store);
    }

    /**
     * Get avatax16 address validation message
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getAvatax16AddressValidationMessage($store)
    {
        return (string)Mage::getStoreConfig(self::PATH_TO_AVATAX16_ADDRESS_VALIDATION_MESSAGE, $store);
    }

    /**
     * Get shipping tax class
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getShippingTaxCode($store)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_TO_AVATAX_SHIPPING_TAX_CODE, $store);
    }

    /**
     * Get companies for account
     *
     * @param       $storeId
     * @param array $params
     * @return array
     */
    public function getAccountCompanies($storeId, $params = array())
    {
        $companies = $this->_getServiceConfig()->getAccountCompanies($storeId, $params);

        return (array)$companies;
    }

    /**
     * Get Service Config
     *
     * @return OnePica_AvaTax_Model_Service_Avatax_Config
     */
    protected function _getServiceConfig()
    {
        return Mage::getModel('avatax/service_avatax_config');
    }
}
