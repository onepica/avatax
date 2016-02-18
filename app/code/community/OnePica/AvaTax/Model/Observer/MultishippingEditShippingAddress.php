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
 * Avatax Observer ControllerActionPredispatchCheckoutMultishippingAddressEditShippingPost
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Observer_MultishippingEditShippingAddress
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Validates customer address during copying address to quote and
     * prevent copying if address validation fails
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     * @throws Mage_Core_Controller_Varien_Exception
     */
    public function preDispatchEditShippingPost(Varien_Event_Observer $observer)
    {
        try
        {
            /* @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $storeId = $quote->getStoreId();

            $customerAddressId = $observer->getControllerAction()->getRequest()->getParam('id');
            if ($customerAddressId) {
                //make a copy of current editable quote shipping address for validation
                $customerAddress = Mage::getModel('checkout/type_multishipping')
                    ->getCustomer()
                    ->getAddressById($customerAddressId);
                $shippingAddress = clone $quote->getShippingAddressByCustomerAddressId($customerAddressId);
                $shippingAddress->importCustomerAddress($customerAddress);

                //perform quote shipping address validation
                $errors = array();
                $normalized = false;

                $message = Mage::getStoreConfig('tax/avatax/validate_address_message', $storeId);

                if ($shippingAddress->validate() !== true) {
                    $errors[] = sprintf($message, $shippingAddress->format('oneline'));
                }
                if ($shippingAddress->getAddressNormalized()) {
                    $normalized = true;
                }

                $session = Mage::getSingleton('checkout/session');
                if ($normalized) {
                    $session->addNotice(Mage::getStoreConfig('tax/avatax/multiaddress_normalize_message', $storeId));
                }

                if (!empty($errors)) {
                    throw new OnePica_AvaTax_Exception(implode('<br />', $errors));
                }
            }
        }
        catch (OnePica_AvaTax_Exception $e)
        {
            //clear all messages
            Mage::getSingleton('customer/session')->getMessages(true);
            //add validation error message
            Mage::getSingleton('customer/session')->addError($e->getMessage());

            //perform forward
            $params = $observer->getControllerAction()->getRequest()->getParams();
            $exception = new Mage_Core_Controller_Varien_Exception();
            $exception->prepareForward(
                'editShipping', null, null,
                array_merge(
                    $params, array('restore_customer_address' => true)
                )
            );
            throw $exception;
        }
        return $this;
    }

    /**
     * Restore customer address from quote shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function postDispatchEditShipping(Varien_Event_Observer $observer)
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
