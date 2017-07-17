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
 * The Onepage Payment Method Available block
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Onepage_Payment_Method_Available
    extends OnePica_AvaTax_Block_Checkout_Onepage_Method
{
    /**
     * Normalization Disabler Block Name
     * @var null
     */
    protected $_disablerBlockName = 'avatax/checkout_onepage_address_normalization_payment_disabler';

    /**
     * Check if Normalization Notification Is Allowed on current checkout step
     *
     * @return bool
     */
    protected function _showNotification()
    {
        $quote = $this->getQuote();
        $isVirtualCheckout = $quote->getItemVirtualQty() == $quote->getItemsQty();

        return $isVirtualCheckout;
    }

    /**
     * Get quote address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getBillingAddress();
        }

        return $this->_address;
    }

}
