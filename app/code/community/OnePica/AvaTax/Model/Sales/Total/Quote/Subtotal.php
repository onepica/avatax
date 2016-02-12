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
 * @copyright  Copyright (c) 2016 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class OnePica_AvaTax_Model_Sales_Total_Quote_Subtotal
 */
class OnePica_AvaTax_Model_Sales_Total_Quote_Subtotal extends Mage_Tax_Model_Sales_Total_Quote_Subtotal
{
    /**
     * Calculate item price including/excluding tax, row total including/excluding tax
     * and subtotal including/excluding tax.
     * Determine discount price if needed
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     *
     * @return  OnePica_AvaTax_Model_Sales_Total_Quote_Subtotal
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $storeId = $address->getQuote()->getStore()->getId();
        if ($this->_getDataHelper()->isServiceEnabled($storeId)) {
            return $this;
        }

        return parent::collect($address);
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

