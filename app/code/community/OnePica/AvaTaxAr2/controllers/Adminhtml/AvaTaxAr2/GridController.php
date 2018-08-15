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
 * Admin grid controller
 *
 * @property \Mage_Adminhtml_Model_Session _sessionAdminhtml
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Adminhtml_AvaTaxAr2_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     */
    protected function _construct()
    {
        $this->setUsedModuleName('OnePica_AvaTaxAr2');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('avataxar2');
    }

    /**
     * Documents grid
     */
    public function documentsAction()
    {
        $this->_initCustomer();

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Document delete
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function documentDeleteAction()
    {
        $certId = $this->getRequest()->getPost('certId');
        $customerId = $this->getRequest()->getPost('customerId');

        $dataResponse = array();
        try {
            $this->_getServiceCertificate()->deleteCertificate($certId, $customerId);
            $dataResponse['success'] = true;
            $dataResponse['message'] = $this->__('Certificate with ID "%s" deleted successfully', $certId);
        } catch (Exception $exception) {
            $dataResponse['success'] = false;
            $dataResponse['message'] = $exception->getMessage();
            $this->getResponse()->setHttpResponseCode(400);
        }

        $this->getResponse()->setBody($this->_getCoreHelper()->jsonEncode($dataResponse));
    }

    /**
     * Documents mass delete
     */
    public function documentMassDeleteAction()
    {
        $certsToDelete = $this->getRequest()->getParam('documents');
        $customerId = $this->getRequest()->getParam('customerId');
        $customerCode = $this->getRequest()->getParam('customerCode');
        $activeTab = $this->getRequest()->getParam('activeTab');

        if (!$certsToDelete) {
            $this->_getAdminhtmlSession()->addError($this->__('Please select document(s).'));
            $this->_redirect('adminhtml/customer/edit', array('id' => $customerId, 'tab' => $activeTab));

            return;
        }

        try {
            foreach ($certsToDelete as $certId) {
                $this->_getServiceCertificate()->deleteCertificate($certId, $customerCode);
            }

            $this->_getAdminhtmlSession()->addSuccess(
                $this->__('Total of %d record(s) were deleted.', count($certsToDelete))
            );
        } catch (Exception $e) {
            $this->_getAdminhtmlSession()->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/customer/edit', array('id' => $customerId, 'tab' => $activeTab));
    }

    /**
     * Document view
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function documentGetPDFAction()
    {
        $certId = $this->getRequest()->getParam('id');

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
     * @param string $idFieldName
     * @return $this
     * @throws \Mage_Core_Exception
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int)$this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);

        return $this;
    }

    /**
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate
     */
    protected function _getServiceCertificate()
    {
        return Mage::getSingleton('avataxar2/service_avatax_certificate');
    }

    /**
     * @return \Mage_Adminhtml_Model_Session
     */
    protected function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return \Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }
}
