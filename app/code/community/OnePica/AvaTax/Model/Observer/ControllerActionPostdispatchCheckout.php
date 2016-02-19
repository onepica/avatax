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
 * Avatax Observer ControllerActionPostdispatchCheckout
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_ControllerActionPostdispatchCheckout
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
        /* @var $quote Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($quote->getAllAddresses() as $address) {
            if ($address->getAddressType() == 'shipping') {
                /* @var OnePica_AvaTax_Model_Action_Calculator $calculator */
                $calculator = Mage::getModel(
                    'avatax/action_calculator',
                    array(OnePica_AvaTax_Model_Action_Calculator::ADDRESS_PARAMETER => $address)
                );

                if (!$calculator->isAbleToCalculateTax()) {
                    $this->_getErrorsHelper()->addErrorMessage($quote->getStoreId());
                } else {
                    //message could be set from the previous calculations
                    $this->_getErrorsHelper()->removeErrorMessage();
                }
            }
        }

        return $this;
    }
}
