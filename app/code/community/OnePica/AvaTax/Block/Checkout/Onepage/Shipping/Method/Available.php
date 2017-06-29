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
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Core_Block_Abstract
{
    /**
     * Quote address
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Checkout session
     *
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkout;

    /**
     * Sales quote
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Get quote address
     *
     * @return Mage_Sales_Model_Quote_Address
     */
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
            $notice = Mage::helper('avatax/config')->getOnepageNormalizeMessage(Mage::app()->getStore());
            if ($notice) {
                Mage::getSingleton('core/session')->addNotice($notice);
                $additional .= $this->getMessagesBlock()->getGroupedHtml();
            }
        } elseif ($this->getAddress()->getAddressNotified()) {
            $additional .= $this->getMessagesBlock()->getGroupedHtml();
        }

        $useNormalization = ($this->getAddress()->getAddressNormalized()) ? 1: 0;
        $checked = ($useNormalization) ? "checked='checked'" : '';
        $additional .= "<p>
            <input type='checkbox' name='allow_normalize_shipping_address' id='allow_normalize_shipping_address' value='$useNormalization' class='checkbox' $checked onclick='checkout.avataxReloadShippingMethods();'>
            <label for='allow_normalize_shipping_address'>Use normalized shipping address</label>
            <script type='application/javascript'>
                checkout.avataxReloadShippingMethods = function() {
                    debugger;

                    var request = new Ajax.Request(
                        '/avatax/normalization/update',
                        {
                            method:'post',
                            parameters:{flag:$useNormalization},
                            onSuccess: function(response){
                                debugger;
                                billing.avataxParentOnSave = billing.onSave;
                                billing.onSave = function(response){
                                    debugger;
                                    checkout.reloadStep('billing');
                                    checkout.loadWaiting = false;

                                    shipping.avataxParentOnSave = shipping.onSave;
                                    shipping.onSave = function(response) {
                                        checkout.reloadStep('shipping');
                                        checkout.loadWaiting = false;

                                        this.onSave = this.avataxParentOnSave;
                                        if(this.avataxParentOnSave) return this.avataxParentOnSave(response);
                                    }.bind(shipping);
                                    shipping.save();

                                    this.onSave = this.avataxParentOnSave;
                                    if(this.avataxParentOnSave) return this.avataxParentOnSave(response);
                                }.bind(billing);
                                billing.save();
                                debugger;
                            }
                        }
                    );
                };

            </script>
        </p>";

        return $additional;
    }
}
