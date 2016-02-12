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
            return false;
        }

        return parent::discountTax($store);
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
     * Get avatax data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getDataHelper()
    {
        return Mage::helper('avatax');
    }
}
