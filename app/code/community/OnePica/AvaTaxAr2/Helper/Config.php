<?php
/**
 * OnePica_AvaTax
 * NOTICE OF LICENSE
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
 * The base AvaTaxAr2 Config Helper class
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TO_ACTION = 'tax/avatax_document_management/action';

    const XML_PATH_TO_URL = 'tax/avatax_document_management/url';

    const XML_PATH_TO_ACCOUNT_NUMBER = 'tax/avatax_document_management/account';

    const XML_PATH_TO_KEY = 'tax/avatax_document_management/license';

    const XML_PATH_TO_ECOM_URL = 'tax/avatax_document_management/ecom_url';

    const XML_PATH_TO_ECOM_USERNAME = 'tax/avatax_document_management/ecom_username';

    const XML_PATH_TO_ECOM_PASSWORD = 'tax/avatax_document_management/ecom_password';

    const XML_PATH_TO_ECOM_CLIENT_ID = 'tax/avatax_document_management/ecom_client_id';

    /**
     * Is AvaTaxAr2 enabled.
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isEnabled()
    {
        $websiteId = Mage::app()->getRequest()->get('website');
        $storeId = Mage::app()->getRequest()->get('store');

        /** @var OnePica_AvaTax_Helper_Data $avataxHelper */
        $avataxHelper = Mage::helper('avatax');
        if (!$avataxHelper->isServiceEnabled($storeId)) {
            return false;
        }

        if ($websiteId && !$storeId) {
            return (bool)Mage::app()->getWebsite($websiteId)->getConfig(self::XML_PATH_TO_ACTION);
        }

        return $this->getStatusServiceAction($storeId);
    }

    /**
     * Get avatax status
     *
     * @param int|Mage_Core_Model_Store $storeId
     * @return mixed
     */
    public function getStatusServiceAction($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ACTION, $storeId);
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
        return Mage::getStoreConfig(self::XML_PATH_TO_URL, $store);
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
        return Mage::getStoreConfig(self::XML_PATH_TO_ACCOUNT_NUMBER, $store);
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
        return Mage::getStoreConfig(self::XML_PATH_TO_KEY, $store);
    }

    /**
     * Returns ecom service url
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getEcomUrl($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ECOM_URL, $store);
    }

    /**
     * Returns ecom service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getEcomUsername($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ECOM_USERNAME, $store);
    }

    /**
     * Returns ecom service account id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getEcomPassword($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ECOM_PASSWORD, $store);
    }

    /**
     * Returns client id
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getClientId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TO_ECOM_CLIENT_ID, $store);
    }

    /**
     * Get customer code format attribute
     *
     * @param int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCustomerCodeFormatAttribute($store)
    {
        $avataxConfig = Mage::helper('avatax/config');

        return $avataxConfig->getCustomerCodeFormatAttribute($store);
    }

    /**
     * @param string $country
     * @return bool
     */
    public function isCountrySupported($country)
    {
        $supported = array('US');

        return in_array($country, $supported);
    }
}
