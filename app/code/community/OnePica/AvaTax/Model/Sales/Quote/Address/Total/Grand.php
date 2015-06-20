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
 * Grand totals calculation model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Sales_Quote_Address_Total_Grand extends Mage_Sales_Model_Quote_Address_Total_Grand
{
    /**
     * Collect grand total address amount
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return OnePica_AvaTax_Model_Sales_Quote_Address_Total_Grand
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $grandTotal = $address->getGrandTotal();
        parent::collect($address);

        $shippingAddress = $address->getQuote()->getShippingAddress();
        if ($this->_isAddressActionable($shippingAddress) && $address->getGrandTotal() == $grandTotal) {
            $address->setGrandTotal($address->getGrandTotal() + $address->getTaxAmount());
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseTaxAmount());
        }

        return $this;
    }

    /**
     * Check if address actionable to calculate tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _isAddressActionable($address)
    {
        /** @var OnePica_AvaTax_Helper_Data $helper */
        $helper = Mage::helper('avatax');
        $storeId = $address->getQuote()->getStoreId();
        return $helper->isAddressActionable($address, $storeId, OnePica_AvaTax_Model_Config::REGIONFILTER_TAX);
    }
}
