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
 * Avatax Observer ControllerActionPostdispatchCheckoutMultishippingAddressEditShipping
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_ControllerActionPostdispatchCheckoutMultishippingAddressEditShipping
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Restore customer address from quote shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $restoreCustomerAddress = $observer->getControllerAction()->getRequest()->getParam('restore_customer_address');
        $customerAddressId = $observer->getControllerAction()->getRequest()->getParam('id');
        if ($restoreCustomerAddress && $customerAddressId) {
            $customerAddress = Mage::getModel('checkout/type_multishipping')
                ->getCustomer()
                ->getAddressById($customerAddressId);
            $shippingAddress = Mage::getSingleton('checkout/session')
                ->getQuote()
                ->getShippingAddressByCustomerAddressId($customerAddressId);

            Mage::helper('core')->copyFieldset(
                'sales_convert_quote_address', 'to_customer_address',
                $shippingAddress, $customerAddress
            );
            $customerAddress->save();
        }
    }
}
