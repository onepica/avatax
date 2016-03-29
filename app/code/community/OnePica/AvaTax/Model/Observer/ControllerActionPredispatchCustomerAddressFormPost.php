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
class OnePica_AvaTax_Model_Observer_ControllerActionPredispatchCustomerAddressFormPost
    extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     * Success Url
     */
    const SUCCESS_URL = 'checkout/multishipping_address/editShippingPost';

    /**
     * Error Url
     */
    const ERROR_URL = 'checkout/multishipping_address/editShipping';

    /**
     * Validates customer address during modifying address from
     * multishipping
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        try
        {
            $urlSuccess = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_SUCCESS_URL);
            $urlError = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_ERROR_URL);
            if (strstr($urlSuccess, self::SUCCESS_URL)
                && strstr($urlError, self::ERROR_URL)
            ) {

                $newCustomerAddressId = $request->getParam('id');
                if ($newCustomerAddressId) {

                    /* @var $quote Mage_Sales_Model_Quote */
                    $quote = Mage::getSingleton('checkout/session')->getQuote();
                    $storeId = $quote->getStoreId();

                    $shippingAddress = $quote->getShippingAddressByCustomerAddressId($newCustomerAddressId);

                    $newCustomerAddress = $this->_getFormNewCustomerAddress($request);
                    $errors = $newCustomerAddress->validate();
                    if ($errors !== true && !empty($errors)) {
                        throw new OnePica_AvaTax_Exception(implode('<br />', $errors));
                    }

                    $orgCustomerAddress = $shippingAddress->exportCustomerAddress();
                    $orgCustomerAddress->setData(
                        array_merge(
                            $newCustomerAddress->getData(),
                            $orgCustomerAddress->getData()
                        )
                    );
                    $shippingAddress->importCustomerAddress($newCustomerAddress);

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
                        $session->addNotice(
                            Mage::getStoreConfig('tax/avatax/multiaddress_normalize_message', $storeId)
                        );

                        $controller->setRedirectWithCookieCheck('checkout/multishipping/shipping', array());
                        $controller->setFlag(
                            '', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true
                        );
                    }

                    if (!empty($errors)) {
                        //restore shipping address
                        $shippingAddress->importCustomerAddress($orgCustomerAddress);
                        $shippingAddress->save();
                        throw new OnePica_AvaTax_Exception(implode('<br />', $errors));
                    }

                    //save new customer address
                    $newCustomerAddress->save();
                    $this->_getCustomerSession()->addSuccess($this->_getHelper()->__('The address has been saved.'));
                }
            }
        }
        catch (Exception $e)
        {
            //clear all messages
            $this->_getCustomerSession()->getMessages(true);
            //add validation error message
            $this->_getCustomerSession()->addError($e->getMessage());

            //restore entered form data
            $this->_getCustomerSession()->setAddressFormData($request->getPost());
            //redirect to multishipping address editing
            $controller = $observer->getEvent()->getControllerAction();
            $controller->setRedirectWithCookieCheck(
                self::ERROR_URL,
                array('id' => $request->getParam('id'))
            );
            $controller->setFlag(
                '', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true
            );
        }

        return $this;

    }

    /**
     * Get New Customer Address from form
     *
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return mixed
     */
    protected function _getFormNewCustomerAddress($request)
    {
        $customerAddressId = $request->getParam('id');
        $customerAddress = Mage::getModel('checkout/type_multishipping')
            ->getCustomer()
            ->getAddressById($customerAddressId);

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntity($customerAddress);
        $addressData    = $addressForm->extractData($request);
        $customerAddress->setData(array_merge($customerAddress->getData(), $addressData));

        return $customerAddress;
    }

    /**
     * Get Customer Session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get Helper
     *
     * @return OnePica_AvaTax_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avatax');
    }
}
