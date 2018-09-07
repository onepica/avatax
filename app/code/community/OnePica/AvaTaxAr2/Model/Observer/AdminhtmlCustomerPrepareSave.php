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
 * Avatax Observer LoadAvaTaxExternalLib
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Observer_AdminhtmlCustomerPrepareSave extends Mage_Core_Model_Abstract
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $this->_addCustomerFormData($observer);

        return $this;
    }

    /**
     * Add data from OnePica_AvaTaxAr2_Block_Adminhtml_Customer_Exemption
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    protected function _addCustomerFormData($observer)
    {
        /** @var \Mage_Customer_Model_Customer $customer */
        $customer = $observer->getCustomer();

        /** @var \Mage_Core_Controller_Request_Http $request */
        $request = $observer->getRequest();

        /** @var  Mage_Customer_Model_Form $customerForm */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
                     ->setFormCode(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_DOCUMENTS_FORM_CODE)
                     ->ignoreInvisible(false);

        $formData = $customerForm->extractData($request, 'avatax_customer');

        $errors = $customerForm->validateData($formData);

        if ($errors !== true) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }

            $this->_getSession()->setCustomerData($request->getPost());
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/customer/edit', array('id' => $customer->getId())));

            return $this;
        }

        $customerForm->compactData($formData);

        return $this;
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
