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
 * Avatax Observer ControllerActionLayoutLoadBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_ControllerActionLayoutLoadBefore
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Add error message if tax estimation has problems when user estimates post
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $action = $observer->getAction();
        if ($action instanceOf Mage_Checkout_CartController
            && $action->getFullActionName() == 'checkout_cart_index') {
            /* @var Mage_Sales_Model_Quote $quote */
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $this->_addErrorMessage($quote);
        }

        return $this;
    }
}
