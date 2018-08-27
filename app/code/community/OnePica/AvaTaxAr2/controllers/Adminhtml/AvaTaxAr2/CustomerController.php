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
     *  AvaTax Document Tab Code
     */
    const AVATAX_DOCUMENTS_TAB_CODE = 'customer_info_tabs_avataxar2_exemptions';

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
     * Send Invitation action
     *
     * @return $this
     */
    public function sendInvitationAction()
    {
        try {
            $mageCustomerId = $this->getRequest()->getParam('id');
            $customerCode = $this->getRequest()->getParam('customerCode');

            /** @var Mage_Customer_Model_Customer $mageCustomer */
            $mageCustomer = Mage::getModel('customer/customer')->load($mageCustomerId);

            $needToRegister = false;

            if ($customerCode) {
                $this->getHelper()->validateCustomerCodeForHttpRequest($customerCode);
                $avaCustomer = $this->_getServiceCertificate()->getCustomer($customerCode, null, false);
                if ($avaCustomer instanceof OnePica_AvaTaxAr2_Exception_Response) {
                    /** @var  OnePica_AvaTaxAr2_Exception_Response $exception */
                    $exception = $avaCustomer;
                    if ($exception->getResponseCode() == 'EntityNotFoundError') {
                        $needToRegister = true;
                    } else {
                        throw $exception;
                    }
                }
            } else {
                $needToRegister = true;
            }

            if ($needToRegister) {
                $this->_redirect(
                    '*/*/saveCustomerToAvalara',
                    array('id' => $mageCustomer->getId(), 'customerCode' => $customerCode, 'need_invitation' => true)
                );

                return $this;
            }

            $company = $this->_getServiceCertificate()->getCompanyInfo();

            $result = $this->_getServiceCertificate()
                           ->sendCertExpressInvite(
                               $company->getId(),
                               $avaCustomer->getCustomerCode(),
                               $mageCustomer->getEmail()
                           );

            $this->_getCoreSession()->addSuccess($this->_getHelper()->__('Invitation sent.'));

            $this->_saveCustomerCode($mageCustomer->getId(), $customerCode);
        } catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
        }

        $this->_redirect(
            '*/customer/edit', array('id' => $mageCustomer->getId(), 'tab' => self::AVATAX_DOCUMENTS_TAB_CODE)
        );

        return $this;
    }

    /**
     * Save Customer To Avalara Action
     *
     * @return $this
     */
    public function saveCustomerToAvalaraAction()
    {
        try {
            $mageCustomerId = $this->getRequest()->getParam('id');
            $customerCode = $this->getRequest()->getParam('customerCode');
            $needInvitation = $this->getRequest()->getParam('need_invitation');

            /** @var Mage_Customer_Model_Customer $mageCustomer */
            $mageCustomer = Mage::getModel('customer/customer')->load($mageCustomerId);

            $customerCode = $customerCode ? $customerCode : $this->getHelper()->generateCustomerNumber($mageCustomer);
            $this->getHelper()->validateCustomerCodeForHttpRequest($customerCode);

            if ($customerCode) {
                $avaCustomer = $this->_getServiceCertificate()->getCustomer($customerCode, null, false);
                if ($avaCustomer instanceof OnePica_AvaTaxAr2_Exception_Response) {
                    /** @var  OnePica_AvaTaxAr2_Exception_Response $exception */
                    $exception = $avaCustomer;
                    if ($exception->getResponseCode() == 'EntityNotFoundError') {
                        $avaCustomer = null;
                    } else {
                        throw $exception;
                    }
                }
            }

            $data = new \Varien_Object(
                array(
                    'mage_customer'   => $mageCustomer,
                    'customer_code'   => $customerCode,
                    'ava_customer'    => $avaCustomer,
                    'back_url'        => $this->getUrl(
                        '*/customer/edit',
                        array('id' => $mageCustomer->getId(), 'tab' => self::AVATAX_DOCUMENTS_TAB_CODE)
                    ),
                    'need_invitation' => $needInvitation,
                    'is_new'          => $avaCustomer == null
                )
            );

            Mage::register('save_customer_to_avalara', $data);

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
            $this->_redirectReferer();
        }

        return $this;
    }

    /**
     * Register Customer in Avalara Action
     *
     * @return $this
     */
    public function registerAvalaraCustomerAction()
    {
        try {
            $needInvitation = $this->getRequest()->getParam('need_invitation');

            $data = $this->getRequest()->getPost();
            $model = Mage::getModel('avataxar2/service_avatax_model_customer')->setData($data);
            $this->_getServiceCertificate()->createCustomer($model);

            $this->_saveCustomerCode($data['mage_id'], $data['customer_code']);

            $this->_getCoreSession()->addSuccess('Customer was successfully registered in Avalara.');

            if ($needInvitation) {
                $this->_redirect(
                    '*/avaTaxAr2_customer/sendInvitation',
                    array(
                        'id'           => $data['mage_id'],
                        'customerCode' => $data['customer_code'],
                        'tab'          => self::AVATAX_DOCUMENTS_TAB_CODE
                    )
                );
            } else {
                $this->_redirect(
                    '*/customer/edit', array('id' => $data['mage_id'], 'tab' => self::AVATAX_DOCUMENTS_TAB_CODE)
                );
            }
        } catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
            $this->_redirectReferer();
        }

        return $this;
    }

    /**
     * Update Customer in Avalara Action
     *
     * @return $this
     */
    public function updateAvalaraCustomerAction()
    {
        try {
            $data = $this->getRequest()->getPost();
            $model = Mage::getModel('avataxar2/service_avatax_model_customer')->setData($data);
            $this->_getServiceCertificate()->updateCustomer($model);

            $this->_saveCustomerCode($data['mage_id'], $data['customer_code']);

            $this->_getCoreSession()->addSuccess('Customer data was updated in Avalara.');

            $this->_redirect(
                '*/customer/edit', array('id' => $data['mage_id'], 'tab' => 'customer_info_tabs_avataxar2_exemptions')
            );
        } catch (Exception $ex) {
            /* todo : use admin session instead of core session */
            $this->_getCoreSession()->addError($ex->getMessage());
            $this->_redirectReferer();
        }

        return $this;
    }

    /**
     * Save Customer Code
     *
     * @param $customerId
     * @param $customerCode
     * @return $this
     * @throws \Mage_Core_Exception
     */
    protected function _saveCustomerCode($customerId, $customerCode)
    {
        $this->getHelper()->validateCustomerCodeForHttpRequest($customerCode);
        $codeAttribute = $this->_getAvaTaxConfig()->getCustomerCodeFormatAttribute();

        /** @var OnePica_AvaTax_Helper_Config $avaHelper */
        $customer = Mage::getModel('customer/customer');
        $customer->setId($customerId);
        $customer->setData($codeAttribute, $customerCode);
        $customer->getResource()->saveAttribute($customer, $codeAttribute);

        return $this;
    }

    /**
     * Get Core Helper
     *
     * @return \Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }

    /**
     * Get  Helper
     *
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * Get Certificate Service
     *
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate
     */
    protected function _getServiceCertificate()
    {
        return Mage::getSingleton('avataxar2/service_avatax_certificate');
    }

    /**
     * Get Customer Session
     *
     * @return \Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get Core Session
     *
     * @return \Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * Get Admin Session
     *
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * Get AvaTax Configuration Helper
     *
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getAvaTaxConfig()
    {
        return Mage::helper('avatax/config');
    }
}
