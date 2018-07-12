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
 * Customer controller
 *
 * @property \Mage_Adminhtml_Model_Session _sessionAdminhtml
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_DocumentsController extends Mage_Core_Controller_Front_Action
{
    /**
     * Customer documents grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer documents delete action
     */
    public function deleteAction()
    {
        try {
            $docId = $this->getRequest()->getParam('document_id');
            $customer = $this->_getCustomerSession()->getCustomer();
            $customerId = $customer->getData(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_CODE);

            $this->_getServiceCertificate()->deleteCertificate($docId, $customerId);
            $this->_getCoreSession()->addSuccess($this->__('Certificate with ID "%s" deleted successfully', $docId));
        } catch (Exception $exception) {
            $this->_getCoreSession()->addError($exception->getMessage());
        }

        $this->_redirect('*/*/grid');
    }

    public function postFormAction()
    {
        try {
            $customerNumber = $this->getRequest()->getParam('customer_number');
            if (!$customerNumber) {
                Mage::throwException('Customer Number not set');
            }

            $customer = $this->_getCustomerSession()->getCustomer();
            $customer->setData(OnePica_AvaTaxAr2_Helper_Data::AVATAX_CUSTOMER_CODE, $customerNumber);
            $customer->save();

            $this->_getCoreSession()->addSuccess($this->__('Customer number saved successfully'));
        } catch (Exception $exception) {
            $this->_getCoreSession()->addError($exception->getMessage());
        }

        $this->_redirect('*/*/grid');
    }

    /**
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate
     */
    protected function _getServiceCertificate()
    {
        return Mage::getSingleton('avataxar2/service_avatax_certificate');
    }

    /**
     * @return \Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return \Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }
}
