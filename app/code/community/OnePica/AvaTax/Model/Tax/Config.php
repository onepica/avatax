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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::discountTax($store);
    }

    /**
     * Check if product prices inputed include tax
     *
     * @param null|int $store
     * @return bool
     */
    public function priceIncludesTax($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::priceIncludesTax($store);
    }

    /**
     * Check if shipping prices include tax
     *
     * @param null|int $store
     * @return bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::shippingPriceIncludesTax($store);
    }

    /**
     * Check if display cart prices included tax
     *
     * @param null|int $store
     * @return bool
     */
    public function displayCartPricesInclTax($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartPricesInclTax($store);
    }

    /**
     * Check if display cart prices excluded tax
     *
     * @param null|int $store
     * @return bool
     */
    public function displayCartPricesExclTax($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return true;
        }

        return parent::displayCartPricesExclTax($store);
    }

    /**
     * Check if display cart prices included and excluded tax
     *
     * @param null|int $store
     * @return bool
     */
    public function displayCartPricesBoth($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
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
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartShippingBoth($store);
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
