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
     * Check if product prices inputed include tax
     *
     * @param Mage_Core_Model_Store|int $store
     * @return  bool
     */
    public function priceIncludesTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::priceIncludesTax($store);
    }

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
            return true;
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
            return false;
        }

        return parent::shippingPriceIncludesTax($store);
    }

    /**
     * Check if display cart prices included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartPricesInclTax($store);
    }

    /**
     * Check if display cart prices excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displayCartPricesExclTax($store);
    }

    /**
     * Check if display cart prices included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartPricesBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartPricesBoth($store);
    }

    /**
     * Check if display cart subtotal included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartSubtotalInclTax($store);
    }

    /**
     * Check if display cart subtotal excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displayCartSubtotalExclTax($store);
    }

    /**
     * Check if display cart subtotal included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartSubtotalBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartSubtotalBoth($store);
    }

    /**
     * Check if display cart shipping included tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartShippingInclTax($store);
    }

    /**
     * Check if display cart shipping excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displayCartShippingExclTax($store);
    }

    /**
     * Check if display cart shipping included and excluded tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displayCartShippingBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displayCartShippingBoth($store);
    }

    /**
     * Check if display sales prices include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesPricesInclTax($store);
    }

    /**
     * Check if display sales prices exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displaySalesPricesExclTax($store);
    }

    /**
     * Check if display sales prices include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesPricesBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesPricesBoth($store);
    }

    /**
     * Check if display sales subtotal include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesSubtotalInclTax($store);
    }

    /**
     * Check if display sales subtotal exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displaySalesSubtotalExclTax($store);
    }

    /**
     * Check if display sales subtotal include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesSubtotalBoth($store);
    }

    /**
     * Check if display sales shipping include tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingInclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesShippingInclTax($store);
    }

    /**
     * Check if display sales shipping exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingExclTax($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return true;
        }

        return parent::displaySalesShippingExclTax($store);
    }

    /**
     * Check if display sales shipping include and exclude tax
     *
     * @param mixed $store
     * @return bool
     */
    public function displaySalesShippingBoth($store = null)
    {
        if ($this->_getDataHelper()->isServiceEnabled($store)) {
            return false;
        }

        return parent::displaySalesShippingBoth($store);
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
