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
 * The Onepage Shipping Method Available block
 */
class OnePica_AvaTax_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Core_Block_Abstract
{
    protected $_address;

    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }
    
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
    	if (empty($this->_checkout)) {
    		$this->_checkout = Mage::getSingleton('checkout/session');
    	}
    	return $this->_checkout;
    }
    
    /**
     * Retrieve sales quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
    	if (empty($this->_quote)) {
    		$this->_quote = $this->getCheckout()->getQuote();
    	}
    	return $this->_quote;
    }
    
	/**
	 * Overriding parent to insert session message block if an address has been validated.
	 *
	 * @return string
	 */
	protected function _toHtml ()
	{
		$additional = parent::_toHtml();
		if ($this->getAddress()->getAddressNormalized()) {
			$notice = Mage::getSingleton('avatax/config')->getConfig('onepage_normalize_message');
			if ($notice) {
				Mage::getSingleton('core/session')->addNotice($notice);
				$additional .= $this->getMessagesBlock()->getGroupedHtml();
			}
		} else if ($this->getAddress()->getAddressNotified()) {
			$additional .= $this->getMessagesBlock()->getGroupedHtml();
		}
		return $additional;
	}
}
