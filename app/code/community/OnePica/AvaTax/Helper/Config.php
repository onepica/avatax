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
     * Returns a config value from the admin.
     *
     * @param string $path
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return string
     */
    protected function _getConfig($path, $store = null)
    {
        return Mage::getSingleton('avatax/config')->getConfig($path, $store);
    }

    /**
     * Returns full stop on error
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function fullStopOnError($store = null)
    {
        return (bool)$this->_getConfig('error_full_stop', $store);
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
        return $this->_getConfig('shipping_sku', $store);
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
        return $this->_getConfig('gw_order_sku', $store);
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
        return $this->_getConfig('gw_items_sku', $store);
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
        return $this->_getConfig('gw_printed_card_sku', $store);
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
        return $this->_getConfig('sales_person_code', $store);
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
        return $this->_getConfig('location_code', $store);
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
        return $this->_getConfig('adjustment_positive_sku', $store);
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
        return $this->_getConfig('adjustment_negative_sku', $store);
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
        return $this->_getConfig('field_required_list', $store);
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
        return $this->_getConfig('field_rule', $store);
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
        return $this->_getConfig('line_ref1_code', $store);
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
        return $this->_getConfig('line_ref2_code', $store);
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
            return !(bool)Mage::app()->getWebsite($websiteId)->getConfig('tax/avatax/action');
        }

        return !(bool)Mage::getStoreConfig('tax/avatax/action', $storeId);
    }
}
