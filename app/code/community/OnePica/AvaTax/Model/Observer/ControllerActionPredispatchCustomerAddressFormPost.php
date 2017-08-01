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
 * Avatax Observer ControllerActionPredispatchCustomerAddressFormPost
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
     * multi shipping
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if ($this->_haveToProcess($observer)) {
            /** @var Mage_Core_Controller_Request_Http $request */
            $request = $observer->getControllerAction()->getRequest();

            try {
                $requestObjects = $this->_getRequestObjects($request);
                if ($requestObjects) {
                    //validate customer address
                    $errors = $requestObjects->newCustomerAddress->validate();
                    if ($errors !== true && !empty($errors)) {
                        throw new OnePica_AvaTax_Exception(implode('<br />', $errors));
                    }

                    //validate shipping address
                    $validationResults = $this->_validateShippingAddress($requestObjects);
                    if ($validationResults->normalized) {
                        Mage::getSingleton('checkout/session')->addNotice(
                            Mage::getStoreConfig('tax/avatax/multiaddress_normalize_message', $requestObjects->storeId)
                        );

                        $this->_setRedirect($observer, 'checkout/multishipping/shipping', array());
                    }

                    if ($validationResults->hasErrors) {
                        throw new OnePica_AvaTax_Exception(implode('<br />', $validationResults->errors));
                    }

                    //save new customer address
                    $requestObjects->newCustomerAddress->save();
                }
            } catch (Exception $e) {
                //clear all messages
                $this->_getCustomerSession()->getMessages(true);
                //add validation error message
                $this->_getCustomerSession()->addError($e->getMessage());

                //restore entered form data
                $this->_getCustomerSession()->setAddressFormData($request->getPost());
                //redirect to multishipping address editing
                $this->_setRedirect($observer, self::ERROR_URL, array('id' => $request->getParam('id')));
            }
        }

        return $this;
    }

    /**
     * Return true
     * - If customer edit address on multishipping second step
     *
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    protected function _haveToProcess(Varien_Event_Observer $observer)
    {
        $request = $observer->getControllerAction()->getRequest();

        $urlSuccess = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_SUCCESS_URL);
        $urlError = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_ERROR_URL);

        return strstr($urlSuccess, self::SUCCESS_URL) && strstr($urlError, self::ERROR_URL);
    }

    /**
     * Get Current Request Objects
     *
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return null|object
     */
    protected function _getRequestObjects(Mage_Core_Controller_Request_Http $request)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $newCustomerAddressId = $request->getParam('id');
        if (!$newCustomerAddressId) {
            return null;
        }

        $shippingAddress = $quote->getShippingAddressByCustomerAddressId($newCustomerAddressId);
        if (!$shippingAddress) {
            return null;
        }

        $newCustomerAddress = $this->_getFormNewCustomerAddress($request);

        $orgCustomerAddress = $shippingAddress->exportCustomerAddress();
        $orgCustomerAddress->setData(
            array_merge(
                $newCustomerAddress->getData(),
                $orgCustomerAddress->getData()
            )
        );

        return (object)
        array('quote'              => $quote,
              'storeId'            => $quote->getStoreId(),
              'shippingAddress'    => $shippingAddress,
              'orgCustomerAddress' => $orgCustomerAddress,
              'newCustomerAddress' => $newCustomerAddress);
    }

    /**
     * Get New Customer Address from form
     *
     * @param Mage_Core_Controller_Request_Http $request
     *
     * @return Mage_Customer_Model_Address
     */
    protected function _getFormNewCustomerAddress(Mage_Core_Controller_Request_Http $request)
    {
        $customerAddressId = $request->getParam('id');
        $customerAddress = Mage::getModel('checkout/type_multishipping')
            ->getCustomer()
            ->getAddressById($customerAddressId);

        /** @var Mage_Customer_Model_Form $addressForm*/
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')->setEntity($customerAddress);
        $addressData = $addressForm->extractData($request);
        $customerAddress->setData(array_merge($customerAddress->getData(), $addressData));
        $customerAddress
            ->setIsDefaultBilling($request->getParam('default_billing', false))
            ->setIsDefaultShipping($request->getParam('default_shipping', false));

        return $customerAddress;
    }

    /**
     * Validate shipping address
     *
     * @param object $requestObjects
     *
     * @return object
     */
    protected function _validateShippingAddress($requestObjects)
    {
        $storeId = $requestObjects->storeId;
        $shippingAddress = $requestObjects->shippingAddress;
        $newCustomerAddress = $requestObjects->newCustomerAddress;
        $orgCustomerAddress = $requestObjects->orgCustomerAddress;

        //perform quote shipping address validation
        $errors = array();
        $normalized = false;

        $shippingAddress->importCustomerAddress($newCustomerAddress);

        if ($shippingAddress->validate() !== true) {
            $message = Mage::getStoreConfig('tax/avatax/validate_address_message', $storeId);
            $errors[] = sprintf($message, $shippingAddress->format('oneline'));
        }

        if ($shippingAddress->getAddressNormalized()) {
            $normalized = true;
        }

        if (!empty($errors)) {
            //restore shipping address on error, address saved, if normilization turned on
            $shippingAddress->importCustomerAddress($orgCustomerAddress);
            $shippingAddress->save();
        }

        return (object)
        array(
            'hasErrors'  => !empty($errors),
            'errors'     => $errors,
            'normalized' => $normalized);
    }

    /**
     * Set Redirect
     *
     * @param Varien_Event_Observer $observer
     * @param string $path
     * @param array $params
     *
     * @return $this
     */
    protected function _setRedirect(Varien_Event_Observer $observer, $path, $params)
    {
        $controller = $observer->getControllerAction();
        $controller->setRedirectWithCookieCheck($path, $params);
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

        return $this;
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
