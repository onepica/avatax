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
 * Avatax Observer AdminSystemConfigSaveBefore
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTaxAr2_Model_Observer_AdminCustomerSaveBefore extends OnePica_AvaTax_Model_Observer_Abstract
{
    /**
     *  Validates AvaTax configuration
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function execute(Varien_Event_Observer $observer)
    {
        try {
            if ($this->_getConfigHelper()->isEnabled()) {
                /** @var Mage_Customer_Model_Customer $customer */
                $customer = $observer->getEvent()->getCustomer();
                $customerCode = $this->_getHelper()->getCustomerNumber($customer);

                if ($customerCode) {
                    $this->_getHelper()->validateCustomerCodeForHttpRequest($customerCode);
                    $avaCustomer = $this->_getServiceCertificate()->getCustomer($customerCode, null, false);
                    if ($avaCustomer instanceof OnePica_AvaTaxAr2_Exception_Response) {
                        /** @var  OnePica_AvaTaxAr2_Exception_Response $exception */
                        $exception = $avaCustomer;
                        if ($exception->getResponseCode() == 'EntityNotFoundError') {
                            $this->_getCoreSession()->addWarning($avaCustomer->getMessage());
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            if ($customer) {
                $customerCodeAttr = $this->_getConfigHelper()->getCustomerCodeFormatAttribute(Mage::app()->getStore());
                $this->_getHelper()->setCustomerNumber($customer, $customer->getOrigData($customerCodeAttr));
            }

            $this->_getCoreSession()->addError($ex->getMessage());
        }

        return $this;
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('avataxar2');
    }

    /**
     * @return \OnePica_AvaTaxAr2_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('avataxar2/config');
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
     * Get Certificate Service
     *
     * @return OnePica_AvaTaxAr2_Model_Service_Avatax_Certificate
     */
    protected function _getServiceCertificate()
    {
        return Mage::getSingleton('avataxar2/service_avatax_certificate');
    }
}
