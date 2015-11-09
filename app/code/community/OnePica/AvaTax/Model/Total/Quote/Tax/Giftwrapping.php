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
 * Class OnePica_AvaTax_Model_Total_Quote_Tax_Giftwrapping
 */
class OnePica_AvaTax_Model_Total_Quote_Tax_Giftwrapping
    extends Enterprise_GiftWrapping_Model_Total_Quote_Tax_Giftwrapping
{
    /**
     * Collect gift wrapping tax totals
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        $storeId = $address->getQuote()->getStore()->getId();
        if ($this->_getAvaTaxDataHelper()->isServiceEnabled($storeId)) {
            return $this;
        }

        return parent::collect($address);
    }

    /**
     * Get avatax data helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getAvaTaxDataHelper()
    {
        return Mage::helper('avatax');
    }
}
