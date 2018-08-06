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
 * popup controller
 *
 * @property \Mage_Adminhtml_Model_Session _sessionAdminhtml
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Adminhtml_AvaTaxAr2_PopupController extends Mage_Adminhtml_Controller_Action
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

    public function genCertAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Generates token with given customer number
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function getTokenAction()
    {
        /** @var OnePica_AvaTaxAr2_Model_Service_Ecom_Config $config */
        $config = Mage::getModel('avataxar2/service_ecom_config');
        $client = $config->getClient();

        $responseData = array();

        try {
            $customerNumber = $this->getRequest()->getPost('customerNumber');
            if (!$customerNumber) {
                Mage::throwException($this->__('Customer number is not set'));
            }

            $response = $client->getToken($customerNumber);
            $responseData['token'] = $response->token;
            $responseData['success'] = true;
        } catch (Exception $e) {
            $responseData['message'] = $e->getMessage();
            $responseData['success'] = false;
            $this->getResponse()->setHttpResponseCode(400);
        }

        $this->getResponse()->setBody($this->_getCoreHelper()->jsonEncode($responseData));
    }

    /**
     * Generates token with given customer number
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function certCreateAfterAction()
    {
        $responseData = array();

        try {
            $customerId = $this->getRequest()->getPost('customerId');
            $customerNumber = $this->getRequest()->getPost('customerNumber');

            if (!$customerId || !$customerNumber) {
                Mage::throwException('Required data is not set');
            }

            $customer = Mage::getModel('customer/customer')->load($customerId);
            $this->_getHelper()->setCustomerNumber($customerNumber, $customerNumber);
            $customer->save();

            $responseData['success'] = true;
        } catch (Exception $e) {
            $responseData['success'] = false;
            $responseData['message'] = $e->getMessage();
            $this->getResponse()->setHttpResponseCode(400);
        }

        $this->getResponse()->setBody($this->_getCoreHelper()->jsonEncode($responseData));
    }

    /**
     * @return \Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }
}
