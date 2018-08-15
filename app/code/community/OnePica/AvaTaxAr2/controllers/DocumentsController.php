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
     *
     * @throws \Mage_Core_Exception
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
            $customerId = $this->_getHelper()->getCustomerNumber($customer);

            $this->_getServiceCertificate()->deleteCertificate($docId, $customerId);
            $this->_getCoreSession()->addSuccess($this->__('Certificate with ID "%s" deleted successfully', $docId));
            $this->_getAvataxSession()->setCertUpdatedDate(Mage::getModel('core/date')->date());
        } catch (Exception $exception) {
            $this->_getCoreSession()->addError($exception->getMessage());
        }

        $this->_redirect('*/*/grid');
    }

    /**
     * Document Get PDF
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function documentGetPDFAction()
    {
        $certId = $this->getRequest()->getParam('document_id');

        try {
            $this->getResponse()->setBody($this->_getServiceCertificate()->getCertificatePdf($certId));
            $this->getResponse()->setHeader('Content-Type', 'application/pdf', true);
        } catch (Exception $exception) {
            $this->getResponse()->setHttpResponseCode(400);
            $this->getResponse()->setBody(
                $this->_getCoreHelper()->jsonEncode(array('message' => $exception->getMessage()))
            );
        }
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

    /**
     * @return \OnePica_AvaTax_Model_Session
     */
    protected function _getAvataxSession()
    {
        return Mage::getSingleton('avatax/session');
    }

    /**
     * @return \Mage_Core_Helper_Abstract|\OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return \Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }
}
