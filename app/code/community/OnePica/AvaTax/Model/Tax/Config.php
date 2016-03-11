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
 * Configuration paths storage
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Tax_Config extends Mage_Tax_Model_Config
{
    /**
     * Get configuration setting "Apply Discount On Prices Including Tax" value
     * Always apply discount first since AvaTax does not support line-level item discount amounts
     *
     * @param   null|int $store
     * @return  bool
     */
    public function discountTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                return true;
            }

            return false;
        }

        return parent::discountTax($store);
    }

    /**
     * Check what taxes should be applied after discount
     *
     * @param Mage_Core_Model_Store|int $store
     * @return  bool
     */
    public function applyTaxAfterDiscount($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if (!$this->_getTaxDataHelper()->priceIncludesTax($store)) {
                return true;
            }
        }

        return parent::applyTaxAfterDiscount($store);
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param Mage_Core_Model_Store|int $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                return Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
            }

            return Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
        }

        return parent::getPriceDisplayType($store);
    }

    /**
     * Get shipping methods prices display type
     *
     * @param Mage_Core_Model_Store|int $store
     * @return  int
     */
    public function getShippingPriceDisplayType($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if ($this->_getTaxDataHelper()->shippingPriceIncludesTax($store)) {
                return Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX;
            }

            return Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX;
        }

        return parent::getShippingPriceDisplayType($store);
    }

    /**
     * Return the config value for self::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED
     *
     * @param Mage_Core_Model_Store|int $store
     * @return int
     */
    public function crossBorderTradeEnabled($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                return true;
            }

            return false;
        }

        return parent::crossBorderTradeEnabled($store);
    }

    /**
     * Check if shipping prices include tax
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            if ($this->_getTaxDataHelper()->priceIncludesTax($store)) {
                return true;
            }

            return false;
        }

        return parent::shippingPriceIncludesTax($store);
    }

    /**
     * Get tax data helper
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxDataHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Get avatax data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }
}
