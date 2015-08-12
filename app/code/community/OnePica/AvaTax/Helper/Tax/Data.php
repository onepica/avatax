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
 * Catalog data helper
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Helper_Tax_Data extends Mage_Tax_Helper_Data
{
    /**
     * Avatax shipping tax class
     */
    const AVATAX_SHIPPING_TAX_CLASS = 'FR020100';

    /**
     * Items should not include tax so that AvaTax can calculate it
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function priceIncludesTax($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return false;
        }
        return parent::priceIncludesTax($store);
    }

    /**
     * Shipping should not include tax so that AvaTax can calculate it
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function shippingPriceIncludesTax($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return false;
        }
        return parent::shippingPriceIncludesTax($store);
    }

    /**
     * Returns AvaTax's hard-coded shipping tax class
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getShippingTaxClass($store)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return self::AVATAX_SHIPPING_TAX_CLASS;
        }
        return parent::getShippingTaxClass($store);
    }

    /**
     * AvaTax always computes tax based on ship from and ship to addresses
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getTaxBasedOn($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return 'shipping';
        }
        return parent::getTaxBasedOn($store);
    }

    /**
     * Always apply tax on custom price
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function applyTaxOnCustomPrice($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return true;
        }
        return parent::applyTaxOnCustomPrice($store);
    }

    /**
     * Always apply tax on custom price (not original)
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function applyTaxOnOriginalPrice($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return false;
        }
        return parent::applyTaxOnOriginalPrice($store);
    }

    /**
     * Always apply discount first since AvaTax doesn't support line-level item discount amounts
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function applyTaxAfterDiscount($store = null)
    {
        if (Mage::helper('avatax')->isAvataxEnabled($store)) {
            return true;
        }
        return parent::applyTaxAfterDiscount($store);
    }
}
