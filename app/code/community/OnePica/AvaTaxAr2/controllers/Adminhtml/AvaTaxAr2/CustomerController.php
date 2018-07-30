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
class OnePica_AvaTaxAr2_Adminhtml_AvaTaxAr2_CustomerController extends Mage_Adminhtml_Controller_Action
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

    public function sendInvitationAction()
    {
        try
        {
            $mageCustomerId = $this->getRequest()->getParam('id');
            $customerCode = $this->getRequest()->getParam('customerCode');

            /** @var Mage_Customer_Model_Customer $mageCustomer */
            $mageCustomer = Mage::getModel('customer/customer')->load($mageCustomerId);
            $avaCustomer = $this->_getServiceCertificate()->getCustomer($customerCode, null, false);
            if ($avaCustomer instanceof OnePica_AvaTaxAr2_Exception) {
                /** @var  OnePica_AvaTaxAr2_Exception $exception */
                $exception = $avaCustomer;
                if($exception->getResponseCode() == 'EntityNotFoundError') {
                    $this->_redirect('*/*/saveCustomerToAvalara', array('id' => $mageCustomer->getId(), 'customerCode' => null, 'need_invitation' => true));
                    return $this;
                } else {
                    throw $exception;
                }
            }
            $company = $this->_getServiceCertificate()->getCompanyInfo();


            $result = $this->_getServiceCertificate()
                        ->sendCertExpressInvite(
                            $company->getId(),
                            $avaCustomer->getData('customerCode'),
                            $mageCustomer->getEmail()
                        );
        }
        catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        $this->_getCoreSession()->addSuccess($this->_getHelper()->__('Invitation sent.'));
        $this->_redirect('*/customer/edit', array('id'=>$mageCustomer->getId(), 'tab' => 'customer_info_tabs_avataxar2_exemptions'));

        return $this;
    }

    public function saveCustomerToAvalaraAction()
    {
        try
        {
            $mageCustomerId = $this->getRequest()->getParam('id');
            $customerCode = $this->getRequest()->getParam('customerCode');
            $needInvitation = $this->getRequest()->getParam('need_invitation');

            /** @var Mage_Customer_Model_Customer $mageCustomer */
            $mageCustomer = Mage::getModel('customer/customer')->load($mageCustomerId);
            if ($customerCode) {
                $avaCustomer = $this->_getServiceCertificate()->getCustomer($customerCode, null, false);
                if ($avaCustomer instanceof OnePica_AvaTaxAr2_Exception) {
                    /** @var  OnePica_AvaTaxAr2_Exception $exception */
                    $exception = $avaCustomer;
                    if ($exception->getResponseCode() == 'EntityNotFoundError') {
                        $avaCustomer = null;
                    } else {
                        throw $exception;
                    }
                }
            }

            $data = new \Varien_Object(array(
                'mage_customer' => $mageCustomer,
                'customer_code' => $customerCode,
                'ava_customer' => $avaCustomer,
                'back_url' => $this->getUrl('*/customer/edit', array('id'=>$mageCustomer->getId(), 'tab' => 'customer_info_tabs_avataxar2_exemptions')),
                'need_invitation' => $needInvitation,
                'is_new' => $avaCustomer == null
            ));

            Mage::register('save_customer_to_avalara', $data);

            $this->loadLayout();
            $this->renderLayout();

        }
        catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        return $this;
    }

    public function registerAvalaraCustomerAction()
    {
        try
        {
            $data = $this->getRequest()->getPost();
            $model = Mage::getModel('avataxar2/service_avatax_model_customer')->setData($data);
            $this->_getServiceCertificate()->createCustomer($model);

            $this->_getCoreSession()->addSuccess('Customer was successfully registered in Avalara.');

            $this->_redirect('*/customer/edit', array('id'=>$data['mage_id'], 'tab' => 'customer_info_tabs_avataxar2_exemptions'));
        }
        catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        return $this;
    }

    public function updateAvalaraCustomerAction()
    {
        try
        {
            $data = $this->getRequest()->getPost();
            $model = Mage::getModel('avataxar2/service_avatax_model_customer')->setData($data);
            $this->_getServiceCertificate()->updateCustomer($model);

            $this->_getCoreSession()->addSuccess('Customer data was updated in Avalara.');

            $customer = Mage::getModel('customer/customer');
            $customer->setId($data['mage_id']);
            $customer->setData('avatax_customer_code', $data['customer_code']);
            $customer->getResource()->saveAttribute($customer, 'avatax_customer_code');

            $this->_redirect('*/customer/edit', array('id'=>$data['mage_id'], 'tab' => 'customer_info_tabs_avataxar2_exemptions'));
        }
        catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        return $this;
    }

    /**
     * @return \Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
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
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }
}
