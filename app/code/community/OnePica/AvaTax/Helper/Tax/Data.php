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
class OnePica_AvaTax_Helper_Tax_Data extends OnePica_AvaTax_Helper_Tax_Data_Abstract
{
    /** @var null|OnePica_AvaTax_Helper_Data $_helperAvaTax */
    protected $_helperAvaTax = null;

    /** @var null|OnePica_AvaTax_Helper_Config $_helperAvaTaxConfig */
    protected $_helperAvaTaxConfig = null;

    /**
     * @return OnePica_AvaTax_Helper_Data|Mage_Core_Helper_Abstract|null
     */
    protected function _getHelperAvaTax()
    {
        if ($this->_helperAvaTax === null) {
            $this->_helperAvaTax = Mage::helper('avatax');
        }

        return $this->_helperAvaTax;
    }

    /**
     * @return OnePica_AvaTax_Helper_Config|Mage_Core_Helper_Abstract|null
     */
    protected function _getHelperAvaTaxConfig()
    {
        if ($this->_helperAvaTaxConfig === null) {
            $this->_helperAvaTaxConfig = Mage::helper('avatax/config');
        }

        return $this->_helperAvaTaxConfig;
    }

    /**
     * Returns AvaTax's hard-coded shipping tax class
     *
     * @param null|bool|int|Mage_Core_Model_Store $store
     * @return string
     */
    public function getShippingTaxClass($store)
    {
        if ($this->_getHelperAvaTax()->isServiceEnabled($store)) {
            return $this->_getHelperAvaTaxConfig()->getShippingTaxCode($store);
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
        if ($this->_getHelperAvaTax()->isServiceEnabled($store)) {
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
        if ($this->_getHelperAvaTax()->isServiceEnabled($store)) {
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
        if ($this->_getHelperAvaTax()->isServiceEnabled($store)) {
            return false;
        }

        return parent::applyTaxOnOriginalPrice($store);
    }
}
