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
 * @copyright  Copyright (c) 2015 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Helper_GiftWrapping_Data
 */
class OnePica_AvaTax_Helper_GiftWrapping_Data extends Enterprise_GiftWrapping_Helper_Data
{
    /**
     * Check ability to display prices including tax for gift wrapping in shopping cart
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function displayCartWrappingIncludeTaxPrice($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartWrappingIncludeTaxPrice($store);
    }

    /**
     * Check ability to display prices excluding tax for gift wrapping in shopping cart
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function displayCartWrappingExcludeTaxPrice($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return true;
        }

        return parent::displayCartWrappingExcludeTaxPrice($store);
    }

    /**
     * Check ability to display both prices for gift wrapping in shopping cart
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function displayCartWrappingBothPrices($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartWrappingBothPrices($store);
    }

    /**
     * Check ability to display prices including tax for printed card in shopping cart
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function displayCartCardIncludeTaxPrice($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartCardIncludeTaxPrice($store);
    }

    /**
     * Check ability to display both prices for printed card in shopping cart
     *
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     */
    public function displayCartCardBothPrices($store = null)
    {
        if ($this->_getDataHelper()->isAvataxEnabled($store)) {
            return false;
        }

        return parent::displayCartCardBothPrices($store);
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
